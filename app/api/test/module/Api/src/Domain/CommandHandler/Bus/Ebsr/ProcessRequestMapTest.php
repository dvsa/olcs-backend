<?php

/**
 * ProcessRequestMap Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\ORM\Query;
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
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Http\Header\ContentSecurityPolicy;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use org\bovigo\vfs\vfsStream;

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
                    ]
                ]
            ]
        ];

        $this->mockedSmServices = [
            TemplateBuilder::class => m::mock(TemplateBuilder::class),
            FileProcessorInterface::class => m::mock(FileProcessorInterface::class),
            TransExchangeClient::class => m::mock(TransExchangeClient::class),
            'Config' => $config,
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            'FileUploader' => m::mock(ContentStoreFileUploader::class)
        ];

        parent::setUp();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;
        $licenceId = 77;
        $submissionId = 55;
        $emailParams = ['id' => $submissionId];
        $scale = 'small';
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
            ->with($documentIdentifier)
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

        $documentData = [
            'content' => base64_encode(file_get_contents($transxchangeFilename)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_OTHER_DOCUMENTS,
            'filename' => basename($transxchangeFilename),
            'description' => 'TransXchange file',
            'user' => 1
        ];

        $this->expectedSideEffect(UploadCmd::class, $documentData, $docUploadResult);

        $txcPdfData = [
            'id' => $id,
            'document' => $uploadedDocumentId
        ];

        $this->expectedSideEffect(UpdateTxcInboxPdfCmd::class, $txcPdfData, new Result());

        $taskData = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => sprintf(ProcessRequestMap::TASK_SUCCESS_DESC, $busRegNo),
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
     * test handleCommand creates failure task when exception thrown
     * @expectedException \Exception
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
            ->with($documentIdentifier)
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

        $taskData = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => sprintf(ProcessRequestMap::TASK_FAIL_DESC, $busRegNo),
            'actionDate' => date('Y-m-d'),
            'busReg' => $id,
            'licence' => $licenceId,
        ];

        $this->expectedSideEffect(CreateTaskCmd::class, $taskData, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
