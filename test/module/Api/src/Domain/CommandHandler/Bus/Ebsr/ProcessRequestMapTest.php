<?php

/**
 * ProcessRequestMap Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap as ProcessRequestMapCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\ProcessRequestMap;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\UpdateTxcInboxPdf as UpdateTxcInboxPdfCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRequestMap as SendEbsrRequestMapCmd;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Olcs\XmlTools\Xml\TemplateBuilder;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use org\bovigo\vfs\vfsStream;
use Zend\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;

/**
 * ProcessRequestMap Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ProcessRequestMapTest extends CommandHandlerTestCase
{
    protected $template;
    protected $templatePath;
    protected $templatePaths;

    public function setUp()
    {
        $this->sut = new ProcessRequestMap();
        $this->mockRepo('Bus', BusRepo::class);

        $config = [
            'ebsr' => [
                'transexchange_publisher' => [
                    'templates' => [
                        $this->template => 'template path'
                    ],
                ],
                'tmp_extra_path' => '/tmp/file/path'
            ]
        ];

        $this->mockedSmServices = [
            TemplateBuilder::class => m::mock(TemplateBuilder::class),
            FileProcessorInterface::class => m::mock(FileProcessor::class)->makePartial(),
            TransExchangeClient::class => m::mock(TransExchangeClient::class),
            'Config' => $config,
            'FileUploader' => m::mock(ContentStoreFileUploader::class)
        ];

        parent::setUp();
    }

    /**
     * testHandleCommand
     *
     * @dataProvider handleCommandProvider
     */
    public function testHandleCommand($template, $pdfType, $scale, $extraDocumentDesc)
    {
        //use different config from the rest of the tests
        $config = [
            'ebsr' => [
                'transexchange_publisher' => [
                    'templates' => [
                        $template => 'template path'
                    ],
                ],
                'tmp_extra_path' => '/tmp/file/path'
            ]
        ];

        $this->sut->setConfig($config);

        $id = 99;
        $licenceId = 77;
        $submissionId = 55;
        $emailParams = ['id' => $submissionId];
        $xmlFilename = 'filename.xml';
        $documentIdentifier = 'identifier';
        $uploadedDocumentId = 55;
        $busRegNo = 'PB8593040/4896';
        $xmlTemplate = '<xml></xml>';

        $fileSystem = vfsStream::setup();
        $transxchangeFilename = vfsStream::url('root/transxchange.pdf');
        $transxchangeContent = 'doc content';
        $file = vfsStream::newFile('transxchange.pdf');
        $file->setContent($transxchangeContent);
        $fileSystem->addChild($file);

        $transExchangeDocument = [
            'files' => [
                0 => $transxchangeFilename
            ]
        ];

        $command = ProcessRequestMapCmd::create(
            [
                'id' => $id,
                'template' => $template,
                'scale' => $scale,
                'user' => 1
            ]
        );

        $submission = m::mock(EbsrSubmissionEntity::class);
        $submission->shouldReceive('getDocument->getIdentifier')->andReturn($documentIdentifier);
        $submission->shouldReceive('getId')->andReturn($submissionId);

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getId')->andReturn($id);
        $busReg->shouldReceive('getRegNo')->andReturn($busRegNo);
        $busReg->shouldReceive('getLicence->getId')->andReturn($licenceId);
        $busReg->shouldReceive('getEbsrSubmissions')->once()->andReturn(new ArrayCollection([$submission]));

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($busReg);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->once()
            ->with($documentIdentifier, true)
            ->andReturn($xmlFilename);

        $this->mockedSmServices[TemplateBuilder::class]
            ->shouldReceive('buildTemplate')
            ->once()
            ->andReturn($xmlTemplate);

        $this->mockedSmServices[TransExchangeClient::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->with($xmlTemplate)
            ->andReturn($transExchangeDocument);

        $docUploadResult = new Result();
        $docUploadResult->addId('document', $uploadedDocumentId);

        $documentDesc = $this->sut->getDocumentDescriptions()[$template] . $extraDocumentDesc;

        $documentData = [
            'content' => base64_encode(file_get_contents($transxchangeFilename)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_TRANSXCHANGE_PDF,
            'filename' => basename($transxchangeFilename),
            'description' => $documentDesc,
            'user' => 1
        ];

        $this->expectedSideEffect(UploadCmd::class, $documentData, $docUploadResult);

        $this->txcInboxSideEffect($id, $uploadedDocumentId, $pdfType);

        $taskData = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => sprintf(ProcessRequestMap::TASK_SUCCESS_DESC, $documentDesc, $busRegNo),
            'actionDate' => date('Y-m-d'),
            'busReg' => $id,
            'licence' => $licenceId,
        ];

        $this->expectedSideEffect(CreateTaskCmd::class, $taskData, new Result());

        $this->expectedEmailQueueSideEffect(SendEbsrRequestMapCmd::class, $emailParams, $submissionId, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * data provider for testHandleCommand
     */
    public function handleCommandProvider()
    {
        return [
            [
                TransExchangeClient::REQUEST_MAP_TEMPLATE,
                ProcessRequestMap::TXC_INBOX_TYPE_ROUTE,
                'auto',
                ' (Auto Scale)'
            ],
            [
                TransExchangeClient::REQUEST_MAP_TEMPLATE,
                ProcessRequestMap::TXC_INBOX_TYPE_ROUTE,
                'small',
                ' (Small Scale)'
            ],
            [
                TransExchangeClient::REQUEST_MAP_TEMPLATE,
                ProcessRequestMap::TXC_INBOX_TYPE_ROUTE,
                'large',
                ' (Large Scale)'
            ],
            [TransExchangeClient::DVSA_RECORD_TEMPLATE, ProcessRequestMap::TXC_INBOX_TYPE_PDF, '', ''],
            [TransExchangeClient::TIMETABLE_TEMPLATE, '', '', '']
        ];
    }

    /**
     * creates a txc inbox side effect (avoids doing so for timetables)
     *
     * @param $id
     * @param $uploadedDocumentId
     * @param $pdfType
     */
    private function txcInboxSideEffect($id, $uploadedDocumentId, $pdfType)
    {
        if ($pdfType !== '') {
            $txcPdfData = [
                'id' => $id,
                'document' => $uploadedDocumentId,
                'pdfType' => $pdfType
            ];

            $this->expectedSideEffect(UpdateTxcInboxPdfCmd::class, $txcPdfData, new Result());
        }
    }

    /**
     * test handleCommand throws an exception when transxchange doesn't return a good response
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\TransxchangeException
     */
    public function testHandleCommandFail()
    {
        $id = 99;
        $licenceId = 77;
        $submissionId = 55;
        $scale = 'small';
        $xmlFilename = 'filename.xml';
        $documentIdentifier = 'identifier';
        $busRegNo = 'PB8593040/4896';
        $xmlTemplate = '<xml></xml>';

        $command = ProcessRequestMapCmd::Create(
            [
                'id' => $id,
                'template' => $this->template,
                'scale' => $scale,
                'user' => 1
            ]
        );

        $submission = m::mock(EbsrSubmissionEntity::class);
        $submission->shouldReceive('getDocument->getIdentifier')->andReturn($documentIdentifier);
        $submission->shouldReceive('getId')->andReturn($submissionId);

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getId')->andReturn($id);
        $busReg->shouldReceive('getRegNo')->andReturn($busRegNo);
        $busReg->shouldReceive('getLicence->getId')->andReturn($licenceId);
        $busReg->shouldReceive('getEbsrSubmissions')->once()->andReturn(new ArrayCollection([$submission]));

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($busReg);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->once()
            ->with($documentIdentifier, true)
            ->andReturn($xmlFilename);

        $this->mockedSmServices[TemplateBuilder::class]
            ->shouldReceive('buildTemplate')
            ->once()
            ->andReturn($xmlTemplate);

        $this->mockedSmServices[TransExchangeClient::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->with($xmlTemplate)
            ->andReturn(false);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * test handleCommand throws an exception when transxchange throws a runtime exception
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\TransxchangeException
     */
    public function testHandleCommandTransxchangeRuntimeException()
    {
        $id = 99;
        $licenceId = 77;
        $submissionId = 55;
        $scale = 'small';
        $xmlFilename = 'filename.xml';
        $documentIdentifier = 'identifier';
        $busRegNo = 'PB8593040/4896';
        $xmlTemplate = '<xml></xml>';

        $command = ProcessRequestMapCmd::Create(
            [
                'id' => $id,
                'template' => $this->template,
                'scale' => $scale,
                'user' => 1
            ]
        );

        $submission = m::mock(EbsrSubmissionEntity::class);
        $submission->shouldReceive('getDocument->getIdentifier')->andReturn($documentIdentifier);
        $submission->shouldReceive('getId')->andReturn($submissionId);

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getId')->andReturn($id);
        $busReg->shouldReceive('getRegNo')->andReturn($busRegNo);
        $busReg->shouldReceive('getLicence->getId')->andReturn($licenceId);
        $busReg->shouldReceive('getEbsrSubmissions')->once()->andReturn(new ArrayCollection([$submission]));

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($busReg);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->once()
            ->with($documentIdentifier, true)
            ->andReturn($xmlFilename);

        $this->mockedSmServices[TemplateBuilder::class]
            ->shouldReceive('buildTemplate')
            ->once()
            ->andReturn($xmlTemplate);

        $this->mockedSmServices[TransExchangeClient::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->andThrow(AdapterRuntimeException::class, 'exception message');

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * test handleCommand throws an exception when template config is not found
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\TransxchangeException
     */
    public function testHandleCommandMissingTemplateConfig()
    {
        $config = [
            'ebsr' => [
                'transexchange_publisher' => [
                    'templates' => [],
                ],
                'tmp_extra_path' => '/tmp/file/path'
            ]
        ];

        $this->sut->setConfig($config);

        $id = 99;
        $submissionId = 55;
        $scale = 'small';
        $xmlFilename = 'filename.xml';
        $documentIdentifier = 'identifier';

        $command = ProcessRequestMapCmd::Create(
            [
                'id' => $id,
                'template' => $this->template,
                'scale' => $scale,
                'user' => 1
            ]
        );

        $submission = m::mock(EbsrSubmissionEntity::class);
        $submission->shouldReceive('getDocument->getIdentifier')->andReturn($documentIdentifier);
        $submission->shouldReceive('getId')->andReturn($submissionId);

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getEbsrSubmissions')->once()->andReturn(new ArrayCollection([$submission]));

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($busReg);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->once()
            ->with($documentIdentifier, true)
            ->andReturn($xmlFilename);

        $this->sut->handleCommand($command);
    }

    /**
     * test handleCommand throws an exception when config not found
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\TransxchangeException
     */
    public function testHandleCommandMissingFilePathConfig()
    {
        $command = ProcessRequestMapCmd::create([]);
        $this->sut->setConfig([]);
        $this->sut->handleCommand($command);
    }
}
