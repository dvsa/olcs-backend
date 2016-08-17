<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\ProcessPack;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessPack as ProcessPackCmd;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Domain\Repository\BusRegOtherService as BusRegOtherServiceRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\Olcs\Api\Domain\Repository\LocalAuthority as LocalAuthorityRepo;
use Dvsa\Olcs\Api\Domain\Repository\BusServiceType as BusServiceTypeRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Zend\Filter\FilterPluginManager;
use Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\SubmissionResult as SubmissionResultFilter;
use Zend\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrReceived as SendEbsrReceivedCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as CreateTxcInboxCmd;
use Dvsa\Olcs\Transfer\Command\Document\UpdateDocumentLinks as UpdateDocumentLinksCmd;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateBusFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * ProcessPack Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ProcessPackTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ProcessPack();

        $this->mockRepo('Bus', BusRepo::class);
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);
        $this->mockRepo('Document', DocumentRepo::class);
        $this->mockRepo('BusRegOtherService', BusRegOtherServiceRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('TrafficArea', TrafficAreaRepo::class);
        $this->mockRepo('LocalAuthority', LocalAuthorityRepo::class);
        $this->mockRepo('BusServiceType', BusServiceTypeRepo::class);

        $config = [
            'ebsr' => [
                'tmp_extra_path' => 'tmp/directory/path'
            ]
        ];

        $xmlStructureInput = m::mock(Input::class);
        $busRegInput = m::mock(Input::class);
        $processedDataInput = m::mock(Input::class);
        $shortNoticeInput = m::mock(Input::class);
        $filterManager = m::mock(FilterPluginManager::class);
        $fileProcessor = m::mock(FileProcessor::class);

        $submissionResultFilter = m::mock(SubmissionResultFilter::class);

        $submissionResultFilter
            ->shouldReceive('filter')
            ->once()
            ->andReturn('json string');

        $filterManager
            ->shouldReceive('get')
            ->with(SubmissionResultFilter::class)
            ->andReturn($submissionResultFilter);

        $this->mockedSmServices = [
            'EbsrXmlStructure' => $xmlStructureInput,
            'EbsrBusRegInput' => $busRegInput,
            'EbsrProcessedDataInput' => $processedDataInput,
            'EbsrShortNoticeInput' => $shortNoticeInput,
            'Config' => $config,
            'FileUploader' => m::mock(ContentStoreFileUploader::class),
            'FilterManager' => $filterManager,
            FileProcessorInterface::class => $fileProcessor
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            BusNoticePeriodEntity::class => [
                1 => m::mock(BusNoticePeriodEntity::class),
                2 => m::mock(BusNoticePeriodEntity::class)
            ]
        ];

        $this->refData = [
            EbsrSubmissionEntity::VALIDATING_STATUS,
            EbsrSubmissionEntity::PROCESSING_STATUS,
            EbsrSubmissionEntity::PROCESSED_STATUS,
            'bs_in_part',
            BusRegEntity::STATUS_NEW
        ];

        parent::initReferences();
    }

    /**
     * Tests successful creation of a new bus reg application through EBSR
     */
    public function testHandleCommandNewApplication()
    {
        $xmlName = 'xml file name';
        $xmlDocument = "<xml></xml>";
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentId = 91011;
        $documentDescription = 'document description';
        $licenceId = 121314;
        $savedBusRegId = 151617;
        $submissionTypeId = 'submission type id';
        $organisation = m::mock(OrganisationEntity::class);

        $docIdentifier = 'doc/identifier';
        $document = m::mock(DocumentEntity::class);
        $document->shouldReceive('getIdentifier')->once()->andReturn($docIdentifier);
        $document->shouldReceive('getId')->once()->andReturn($documentId);
        $document->shouldReceive('getDescription')->once()->andReturn($documentDescription);

        $cmdData = [
            'organisation' => $organisationId,
            'id' => $ebsrSubId
        ];

        $command = ProcessPackCmd::create($cmdData);

        $xmlDocContext = ['xml_filename' => $xmlName];

        $parsedLicenceNumber = 'OB1234567';
        $parsedVariationNumber = 666;
        $parsedRouteNumber = '12345';
        $parsedOrganisationEmail = 'foo@bar.com';
        $existingRegNo = 'OB1234567/12345';

        $parsedEbsrData = [
            'licNo' => $parsedLicenceNumber,
            'variationNo' => $parsedVariationNumber,
            'routeNo' => $parsedRouteNumber,
            'organisationEmail' => $parsedOrganisationEmail,
            'existingRegNo' => $existingRegNo,
            'subsidised' => 'bs_in_part',
            'busNoticePeriod' => 1,
            'txcAppType' => 'new',
            'serviceClassifications' => [

            ],
            'trafficAreas' => [

            ],
            'localAuthorities' => [

            ],
            'naptan' => [

            ],
            'documents' => [

            ],
            'otherServiceNumbers' => [

            ]
        ];

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('beginValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::VALIDATING_STATUS])
            ->andReturnSelf();
        $ebsrSubmission->shouldReceive('getId')->andReturn($ebsrSubId);
        $ebsrSubmission->shouldReceive('getOrganisation')->once()->andReturn($organisation);
        $ebsrSubmission->shouldReceive('getDocument')->twice()->andReturn($document);
        $ebsrSubmission->shouldReceive('getEbsrSubmissionType->getId')->once()->andReturn($submissionTypeId);
        $ebsrSubmission->shouldReceive('setLicenceNo')->with($parsedLicenceNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('setVariationNo')->with($parsedVariationNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('setRegistrationNo')->with($parsedRouteNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('isDataRefresh')->twice()->andReturn(false);
        $ebsrSubmission->shouldReceive('setOrganisationEmailAddress')
            ->with($parsedOrganisationEmail)
            ->once()
            ->andReturnSelf();
        $ebsrSubmission->shouldReceive('setBusReg')
            ->with(m::type(BusRegEntity::class))
            ->once()
            ->andReturnSelf();

        $this->repoMap['EbsrSubmission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($ebsrSubmission);

        $this->repoMap['EbsrSubmission']->shouldReceive('save')
            ->times(3)
            ->with(m::type(EbsrSubmissionEntity::class));

        $this->repoMap['Bus']->shouldReceive('save')
            ->once()
            ->with(m::type(BusRegEntity::class))
            ->andReturnUsing(
                function (BusRegEntity $busReg) use (&$savedBusReg) {
                    $busReg->setId(151617);
                    $savedBusReg = $busReg;
                }
            );

        $this->mockInput('EbsrXmlStructure', $xmlName, $xmlDocContext, true, $xmlDocument);

        $busRegInputContext = [
            'submissionType' => $submissionTypeId,
            'organisation' => $organisation
        ];

        $this->repoMap['Bus']->shouldReceive('fetchLatestUsingRegNo')
            ->once()
            ->with($existingRegNo)
            ->andReturnNull();

        $this->mockInput('EbsrBusRegInput', $xmlDocument, $busRegInputContext, true, $parsedEbsrData);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('setSubDirPath')
            ->with('tmp/directory/path')
            ->once();
        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->with($docIdentifier)
            ->once()
            ->andReturn($xmlName);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getLicNo')->twice()->andReturn($parsedLicenceNumber);
        $licence->shouldReceive('getLatestBusRouteNo')->once()->andReturn(12345);
        $licence->shouldReceive('getId')->twice()->andReturn($licenceId);

        $this->repoMap['Licence']->shouldReceive('fetchByLicNoWithoutAdditionalData')
            ->once()
            ->with($parsedLicenceNumber)
            ->andReturn($licence);

        $extraProcessedEbsrData = [
            'subsidised' => $this->refData['bs_in_part'],
            'trafficAreas' => new ArrayCollection(),
            'naptanAuthorities' => new ArrayCollection(),
            'busNoticePeriod' => $this->references[BusNoticePeriodEntity::class][1],
            'localAuthoritys' => new ArrayCollection(),
            'busServiceTypes' => new ArrayCollection()
        ];

        $processedDataOutput = array_merge($parsedEbsrData, $extraProcessedEbsrData);
        $processedContext = ['busReg' => null];

        //this is just validators for the data generated by doctrine, no data is filtered
        $this->mockInput('EbsrProcessedDataInput', $processedDataOutput, $processedContext, true, $processedDataOutput);

        $shortNoticeContext = ['busReg' => m::type(BusRegEntity::class)];

        //this is just validators for short notice information, no data is filtered
        $this->mockInput('EbsrShortNoticeInput', $processedDataOutput, $shortNoticeContext, true, $processedDataOutput);

        $ebsrSubmission->shouldReceive('finishValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSING_STATUS], 'json string')
            ->andReturnSelf();

        $ebsrSubmission->shouldReceive('finishProcessing')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSED_STATUS])
            ->andReturnSelf();

        $this->expectedEmailQueueSideEffect(SendEbsrReceivedCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->successSideEffects($existingRegNo, $savedBusRegId, $licenceId, $documentId);

        $this->sut->handleCommand($command);
    }

    /**
     * @param $inputName
     * @param $inputValue
     * @param $context
     * @param $isValid
     * @param $outputValue
     */
    public function mockInput($inputName, $inputValue, $context, $isValid, $outputValue)
    {
        $this->mockedSmServices[$inputName]
            ->shouldReceive('setValue')
            ->once()
            ->with($inputValue)
            ->andReturnSelf();
        $this->mockedSmServices[$inputName]
            ->shouldReceive('isValid')
            //->with($context)
            ->once()
            ->andReturn($isValid);
        $this->mockedSmServices[$inputName]
            ->shouldReceive('getValue')
            ->once()
            ->andReturn($outputValue);
    }

    /**
     * @param $existingRegNo
     * @param $savedBusRegId
     * @param $licenceId
     * @param $documentId
     */
    private function successSideEffects($existingRegNo, $savedBusRegId, $licenceId, $documentId)
    {
        $this->taskSideEffect($existingRegNo, $savedBusRegId, $licenceId);
        $this->documentLinkSideEffect($documentId, $savedBusRegId, $licenceId);
        $this->requestMapSideEffect($savedBusRegId);
        $this->txcInboxSideEffect($savedBusRegId);
        $this->busFeeSideEffect($savedBusRegId);
    }

    /**
     * @param $existingRegNo
     * @param $savedBusRegId
     * @param $licenceId
     */
    private function taskSideEffect($existingRegNo, $savedBusRegId, $licenceId)
    {
        $taskData = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => 'New application created: ' . $existingRegNo,
            'actionDate' => date('Y-m-d'),
            'busReg' => $savedBusRegId,
            'licence' => $licenceId,
        ];

        $this->expectedSideEffect(CreateTaskCmd::class, $taskData, new Result());
    }

    /**
     * @param $documentId
     * @param $savedBusRegId
     * @param $licenceId
     */
    private function documentLinkSideEffect($documentId, $savedBusRegId, $licenceId)
    {
        $documentLinkData = [
            'id' => $documentId,
            'busReg' => $savedBusRegId,
            'licence' => $licenceId,
        ];

        $this->expectedSideEffect(UpdateDocumentLinksCmd::class, $documentLinkData, new Result());
    }

    /**
     * @param $savedBusRegId
     */
    private function requestMapSideEffect($savedBusRegId)
    {
        $requestMapData = [
            'id' => $savedBusRegId,
            'scale' => 'small'
        ];

        $this->expectedSideEffect(RequestMapQueueCmd::class, $requestMapData, new Result());
    }

    /**
     * @param $savedBusRegId
     */
    private function busFeeSideEffect($savedBusRegId)
    {
        $this->expectedSideEffect(CreateBusFeeCmd::class, ['id' => $savedBusRegId], new Result());
    }

    /**
     * @param $savedBusRegId
     */
    private function txcInboxSideEffect($savedBusRegId)
    {
        $this->expectedSideEffect(CreateTxcInboxCmd::class, ['id' => $savedBusRegId], new Result());
    }

}
