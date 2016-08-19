<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
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
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefreshed as SendEbsrRefreshedCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as CreateTxcInboxCmd;
use Dvsa\Olcs\Transfer\Command\Document\UpdateDocumentLinks as UpdateDocumentLinksCmd;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateBusFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrErrors as SendEbsrErrorsCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\EbsrPackException;

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
            EbsrSubmissionEntity::FAILED_STATUS,
            'bs_in_part',
            BusRegEntity::STATUS_NEW,
            BusRegEntity::STATUS_CANCEL,
            BusRegEntity::STATUS_VAR
        ];

        parent::initReferences();
    }

    /**
     * Tests successful creation of a variation application through EBSR
     *
     * @dataProvider handleVariationProvider
     */
    public function testHandleCommandVariation($txcAppType, $busRegStatus, $taskMessage, $fee)
    {
        $xmlName = 'tmp/directory/path/xml-file-name.xml';
        $xmlDocument = "<xml></xml>";
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentId = 91011;
        $documentDescription = 'document description';
        $licenceId = 121314;
        $variationBusRegId = 151617;
        $submissionTypeId = 'submission type id';
        $organisation = m::mock(OrganisationEntity::class);

        $busServiceTypes = [
            'type1_key' => 'service type 1',
            'type2_key' => 'service type 2'
        ];

        $naptanCodes = ['naptan codes'];

        $trafficArea1 = m::mock(TrafficAreaEntity::class);
        $trafficArea2 = m::mock(TrafficAreaEntity::class);
        $trafficArea3 = m::mock(TrafficAreaEntity::class);

        $parsedTrafficAreas = ['parsed traffic areas'];

        $parsedLocalAuthorities = ['parsed local authorities'];

        $docIdentifier = 'doc/identifier';
        $document = $this->basicDocument($docIdentifier, $documentDescription);
        $document->shouldReceive('getId')->once()->andReturn($documentId);

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

        $otherServiceNumber1 = '123';
        $otherServiceNumber2 = '456';

        $busShortNotice = ['bus short notice'];

        $parsedEbsrData = [
            'licNo' => $parsedLicenceNumber,
            'variationNo' => $parsedVariationNumber,
            'routeNo' => $parsedRouteNumber,
            'organisationEmail' => $parsedOrganisationEmail,
            'existingRegNo' => $existingRegNo,
            'subsidised' => 'bs_in_part',
            'busNoticePeriod' => 1,
            'txcAppType' => $txcAppType,
            'serviceClassifications' => $busServiceTypes,
            'trafficAreas' => $parsedTrafficAreas,
            'localAuthorities' => $parsedLocalAuthorities,
            'naptan' => $naptanCodes,
            'documents' => [

            ],
            'otherServiceNumbers' => [$otherServiceNumber1, $otherServiceNumber2],
            'busShortNotice' => $busShortNotice
        ];

        $extraProcessedEbsrData = [
            'subsidised' => $this->refData['bs_in_part'],
            'trafficAreas' => $this->trafficAreas($parsedTrafficAreas, $trafficArea1, $trafficArea2, $trafficArea3),
            'naptanAuthorities' => $this->naptan($naptanCodes),
            'busNoticePeriod' => $this->references[BusNoticePeriodEntity::class][1],
            'localAuthoritys' => $this->localAuthorities($parsedLocalAuthorities, $trafficArea2, $trafficArea3),
            'busServiceTypes' => $this->busServiceTypes($busServiceTypes)
        ];

        $processedDataOutput = array_merge($parsedEbsrData, $extraProcessedEbsrData);
        $busRegFromData = $processedDataOutput;
        unset($busRegFromData['documents']);
        unset($busRegFromData['variationNo']);

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

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 3);

        $this->mockInput('EbsrXmlStructure', $xmlName, $xmlDocContext, $xmlDocument);

        $busRegInputContext = [
            'submissionType' => $submissionTypeId,
            'organisation' => $organisation
        ];

        $variationBusReg = m::mock(BusRegEntity::class);
        $variationBusReg->shouldReceive('fromData')->once()->with($busRegFromData);
        $variationBusReg->shouldReceive('populateShortNotice')->once();
        $variationBusReg->shouldReceive('setOtherServices')->once()->with(m::type(ArrayCollection::class));
        $variationBusReg->shouldReceive('addOtherServiceNumber')->once()->with($otherServiceNumber1);
        $variationBusReg->shouldReceive('addOtherServiceNumber')->once()->with($otherServiceNumber2);
        $variationBusReg->shouldReceive('getRegNo')->once()->andReturn($existingRegNo);
        $variationBusReg->shouldReceive('getId')->times(4)->andReturn($variationBusRegId);
        $variationBusReg->shouldReceive('getLicence->getId')->times(2)->andReturn($licenceId);
        $variationBusReg->shouldReceive('getIsShortNotice')->once()->andReturn('Y');
        $variationBusReg->shouldReceive('getShortNotice->fromData')->once()->with($busShortNotice);
        $variationBusReg->shouldReceive('setEbsrSubmissions')->once()->with(m::type(ArrayCollection::class));
        $variationBusReg->shouldReceive('getEbsrSubmissions')
            ->times(2)
            ->andReturn(new ArrayCollection([$ebsrSubmission]));
        $variationBusReg->shouldReceive('getStatus->getId')->times(2)->andReturn($busRegStatus);

        $previousBusReg = m::mock(BusRegEntity::class);
        $previousBusReg->shouldReceive('createVariation')
            ->with($this->refData[$busRegStatus], $this->refData[$busRegStatus])
            ->once()
            ->andReturn($variationBusReg);

        $this->repoMap['Bus']->shouldReceive('fetchLatestUsingRegNo')
            ->once()
            ->with($existingRegNo)
            ->andReturn($previousBusReg);

        $this->repoMap['Bus']->shouldReceive('save')
            ->once()
            ->with($variationBusReg)
            ->andReturn($variationBusReg);

        $this->mockInput('EbsrBusRegInput', $xmlDocument, $busRegInputContext, $parsedEbsrData);

        $this->fileProcessor($docIdentifier, $xmlName);

        $processedContext = ['busReg' => $variationBusReg];

        //this is just validators for the data generated by doctrine, no data is filtered
        $this->mockInput('EbsrProcessedDataInput', $processedDataOutput, $processedContext, $processedDataOutput);

        $shortNoticeContext = ['busReg' => $variationBusReg];

        //this is just validators for short notice information, no data is filtered
        $this->mockInput('EbsrShortNoticeInput', $processedDataOutput, $shortNoticeContext, $processedDataOutput);

        $ebsrSubmission->shouldReceive('finishValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSING_STATUS], 'json string')
            ->andReturnSelf();

        $ebsrSubmission->shouldReceive('finishProcessing')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSED_STATUS])
            ->andReturnSelf();

        $this->expectedEmailQueueSideEffect(SendEbsrReceivedCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->successSideEffects($existingRegNo, $variationBusRegId, $licenceId, $documentId, $taskMessage, $fee);

        $this->sut->handleCommand($command);
    }

    /**
     * @return array
     */
    public function handleVariationProvider()
    {
        return [
            ['cancel', BusRegEntity::STATUS_CANCEL, 'New cancellation created: ', false],
            ['chargeableChange', BusRegEntity::STATUS_VAR, 'New variation created: ', true]
        ];
    }

    /**
     * Tests successful creation of a new bus reg application through EBSR
     *
     * @param $isDataRefresh
     * @param $emailCommandClass
     * @param $taskMessage
     *
     * @dataProvider handleNewApplicationProvider
     */
    public function testHandleCommandNewApplication($isDataRefresh, $emailCommandClass, $taskMessage)
    {
        $xmlName = 'tmp/directory/path/xml-file-name.xml';
        $xmlDocument = "<xml></xml>";
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentId = 91011;
        $documentDescription = 'document description';
        $licenceId = 121314;
        $savedBusRegId = 151617;
        $submissionTypeId = 'submission type id';
        $organisation = m::mock(OrganisationEntity::class);

        $busServiceTypes = [
            'type1_key' => 'service type 1',
            'type2_key' => 'service type 2'
        ];

        $naptanCodes = ['naptan codes'];

        $trafficArea1 = m::mock(TrafficAreaEntity::class);
        $trafficArea2 = m::mock(TrafficAreaEntity::class);
        $trafficArea3 = m::mock(TrafficAreaEntity::class);

        $parsedTrafficAreas = ['parsed traffic areas'];

        $parsedLocalAuthorities = ['parsed local authorities'];

        $docIdentifier = 'doc/identifier';
        $document = $this->basicDocument($docIdentifier, $documentDescription);
        $document->shouldReceive('getId')->once()->andReturn($documentId);

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
            'serviceClassifications' => $busServiceTypes,
            'trafficAreas' => $parsedTrafficAreas,
            'localAuthorities' => $parsedLocalAuthorities,
            'naptan' => $naptanCodes,
            'documents' => [

            ],
            'otherServiceNumbers' => ['123', '456']
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
        $ebsrSubmission->shouldReceive('isDataRefresh')->twice()->andReturn($isDataRefresh);
        $ebsrSubmission->shouldReceive('setOrganisationEmailAddress')
            ->with($parsedOrganisationEmail)
            ->once()
            ->andReturnSelf();
        $ebsrSubmission->shouldReceive('setBusReg')
            ->with(m::type(BusRegEntity::class))
            ->once()
            ->andReturnSelf();

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 3);

        $this->repoMap['Bus']->shouldReceive('save')
            ->once()
            ->with(m::type(BusRegEntity::class))
            ->andReturnUsing(
                function (BusRegEntity $busReg) use (&$savedBusReg) {
                    $busReg->setId(151617);
                    $savedBusReg = $busReg;
                }
            );

        $this->mockInput('EbsrXmlStructure', $xmlName, $xmlDocContext, $xmlDocument);

        $busRegInputContext = [
            'submissionType' => $submissionTypeId,
            'organisation' => $organisation
        ];

        $this->repoMap['Bus']->shouldReceive('fetchLatestUsingRegNo')
            ->once()
            ->with($existingRegNo)
            ->andReturnNull();

        $this->mockInput('EbsrBusRegInput', $xmlDocument, $busRegInputContext, $parsedEbsrData);

        $this->fileProcessor($docIdentifier, $xmlName);

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
            'trafficAreas' => $this->trafficAreas($parsedTrafficAreas, $trafficArea1, $trafficArea2, $trafficArea3),
            'naptanAuthorities' => $this->naptan($naptanCodes),
            'busNoticePeriod' => $this->references[BusNoticePeriodEntity::class][1],
            'localAuthoritys' => $this->localAuthorities($parsedLocalAuthorities, $trafficArea2, $trafficArea3),
            'busServiceTypes' => $this->busServiceTypes($busServiceTypes)
        ];

        $processedDataOutput = array_merge($parsedEbsrData, $extraProcessedEbsrData);
        $processedContext = ['busReg' => null];

        //this is just validators for the data generated by doctrine, no data is filtered
        $this->mockInput('EbsrProcessedDataInput', $processedDataOutput, $processedContext, $processedDataOutput);

        $shortNoticeContext = ['busReg' => m::type(BusRegEntity::class)];

        //this is just validators for short notice information, no data is filtered
        $this->mockInput('EbsrShortNoticeInput', $processedDataOutput, $shortNoticeContext, $processedDataOutput);

        $ebsrSubmission->shouldReceive('finishValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSING_STATUS], 'json string')
            ->andReturnSelf();

        $ebsrSubmission->shouldReceive('finishProcessing')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::PROCESSED_STATUS])
            ->andReturnSelf();

        $this->expectedEmailQueueSideEffect($emailCommandClass, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->successSideEffects($existingRegNo, $savedBusRegId, $licenceId, $documentId, $taskMessage, true);

        $this->sut->handleCommand($command);
    }

    /**
     * Data provider for handle new application
     *
     * @return array
     */
    public function handleNewApplicationProvider()
    {
        return [
            [false, SendEbsrReceivedCmd::class, 'New application created: '],
            [true, SendEbsrRefreshedCmd::class, 'Data refresh created: ']
        ];
    }

    /**
     * Tests exception thrown for missing config
     *
     * @expectedException \RuntimeException
     */
    public function testMissingConfigException()
    {
        $this->sut->setConfig([]);

        $ebsrSubId = 1234;
        $organisationId = 5678;

        $cmdData = [
            'organisation' => $organisationId,
            'id' => $ebsrSubId
        ];

        $command = ProcessPackCmd::create($cmdData);

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('beginValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::VALIDATING_STATUS])
            ->andReturnSelf();

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 1);

        $this->sut->handleCommand($command);
    }

    /**
     * Tests document retrieval failure
     */
    public function testFailedDocumentRetrieval()
    {
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $organisation = m::mock(OrganisationEntity::class);

        $docIdentifier = 'doc/identifier';
        $documentDescription = 'document description';
        $document = $this->basicDocument($docIdentifier, $documentDescription);

        $cmdData = [
            'organisation' => $organisationId,
            'id' => $ebsrSubId
        ];

        $command = ProcessPackCmd::create($cmdData);

        $ebsrSubmission = $this->failedEbsrSubmission($ebsrSubId, $organisation, $document);

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 2);

        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('setSubDirPath')
            ->with('tmp/directory/path')
            ->once();
        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->with($docIdentifier)
            ->once()
            ->andThrow(EbsrPackException::class, 'message');

        $expectedErrors = ['upload-failure' => 'message'];

        $this->expectedEmailQueueSideEffect(SendEbsrErrorsCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->sut->handleCommand($command);
    }

    /**
     * Tests failure related to xml structure
     */
    public function testFailedXmlStructure()
    {
        $xmlName = 'tmp/directory/path/xml-file-name.xml';
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentDescription = 'document description';
        $organisation = m::mock(OrganisationEntity::class);

        $docIdentifier = 'doc/identifier';
        $document = $this->basicDocument($docIdentifier, $documentDescription);

        $cmdData = [
            'organisation' => $organisationId,
            'id' => $ebsrSubId
        ];

        $command = ProcessPackCmd::create($cmdData);

        $xmlDocContext = ['xml_filename' => $xmlName];

        $ebsrSubmission = $this->failedEbsrSubmission($ebsrSubId, $organisation, $document);

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 2);

        $this->mockInputFailure('EbsrXmlStructure', $xmlName, $xmlDocContext, ['message']);

        $this->fileProcessor($docIdentifier, $xmlName);

        $this->expectedEmailQueueSideEffect(SendEbsrErrorsCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->sut->handleCommand($command);
    }

    /**
     * Tests a failure from bus reg input filter
     */
    public function testFailedBusRegInput()
    {
        $xmlName = 'tmp/directory/path/xml-file-name.xml';
        $xmlDocument = "<xml></xml>";
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentDescription = 'document description';
        $submissionTypeId = 'submission type id';
        $organisation = m::mock(OrganisationEntity::class);

        $docIdentifier = 'doc/identifier';
        $document = $this->basicDocument($docIdentifier, $documentDescription);

        $cmdData = [
            'organisation' => $organisationId,
            'id' => $ebsrSubId
        ];

        $command = ProcessPackCmd::create($cmdData);

        $xmlDocContext = ['xml_filename' => $xmlName];

        $ebsrSubmission = $this->failedEbsrSubmission($ebsrSubId, $organisation, $document);
        $ebsrSubmission->shouldReceive('getEbsrSubmissionType->getId')->once()->andReturn($submissionTypeId);

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 2);

        $this->mockInput('EbsrXmlStructure', $xmlName, $xmlDocContext, $xmlDocument);

        $busRegInputContext = [
            'submissionType' => $submissionTypeId,
            'organisation' => $organisation
        ];

        $this->mockInputFailure('EbsrBusRegInput', $xmlDocument, $busRegInputContext, ['messages']);

        $this->fileProcessor($docIdentifier, $xmlName);

        $this->expectedEmailQueueSideEffect(SendEbsrErrorsCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->sut->handleCommand($command);
    }

    /**
     * Tests a failure from processed data input filter
     */
    public function testFailedProcessDataInput()
    {
        $xmlName = 'tmp/directory/path/xml-file-name.xml';
        $xmlDocument = "<xml></xml>";
        $ebsrSubId = 1234;
        $organisationId = 5678;
        $documentDescription = 'document description';
        $submissionTypeId = 'submission type id';
        $organisation = m::mock(OrganisationEntity::class);

        $busServiceTypes = [
            'type1_key' => 'service type 1',
            'type2_key' => 'service type 2'
        ];

        $naptanCodes = ['naptan codes'];

        $trafficArea1 = m::mock(TrafficAreaEntity::class);
        $trafficArea2 = m::mock(TrafficAreaEntity::class);
        $trafficArea3 = m::mock(TrafficAreaEntity::class);

        $parsedTrafficAreas = ['parsed traffic areas'];

        $parsedLocalAuthorities = ['parsed local authorities'];

        $docIdentifier = 'doc/identifier';
        $document = $this->basicDocument($docIdentifier, $documentDescription);

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
            'serviceClassifications' => $busServiceTypes,
            'trafficAreas' => $parsedTrafficAreas,
            'localAuthorities' => $parsedLocalAuthorities,
            'naptan' => $naptanCodes,
            'documents' => [

            ],
            'otherServiceNumbers' => ['123', '456']
        ];

        $ebsrSubmission = $this->failedEbsrSubmission($ebsrSubId, $organisation, $document);
        $ebsrSubmission->shouldReceive('getEbsrSubmissionType->getId')->once()->andReturn($submissionTypeId);
        $ebsrSubmission->shouldReceive('setLicenceNo')->with($parsedLicenceNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('setVariationNo')->with($parsedVariationNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('setRegistrationNo')->with($parsedRouteNumber)->once()->andReturnSelf();
        $ebsrSubmission->shouldReceive('setOrganisationEmailAddress')
            ->with($parsedOrganisationEmail)
            ->once()
            ->andReturnSelf();

        $this->ebsrSubmissionRepo($command, $ebsrSubmission, 2);

        $this->mockInput('EbsrXmlStructure', $xmlName, $xmlDocContext, $xmlDocument);

        $busRegInputContext = [
            'submissionType' => $submissionTypeId,
            'organisation' => $organisation
        ];

        $this->repoMap['Bus']->shouldReceive('fetchLatestUsingRegNo')
            ->once()
            ->with($existingRegNo)
            ->andReturnNull();

        $this->mockInput('EbsrBusRegInput', $xmlDocument, $busRegInputContext, $parsedEbsrData);

        $extraProcessedEbsrData = [
            'subsidised' => $this->refData['bs_in_part'],
            'trafficAreas' => $this->trafficAreas($parsedTrafficAreas, $trafficArea1, $trafficArea2, $trafficArea3),
            'naptanAuthorities' => $this->naptan($naptanCodes),
            'busNoticePeriod' => $this->references[BusNoticePeriodEntity::class][1],
            'localAuthoritys' => $this->localAuthorities($parsedLocalAuthorities, $trafficArea2, $trafficArea3),
            'busServiceTypes' => $this->busServiceTypes($busServiceTypes)
        ];

        $processedDataInput = array_merge($parsedEbsrData, $extraProcessedEbsrData);
        $processedContext = ['busReg' => null];

        $this->mockInputFailure('EbsrProcessedDataInput', $processedDataInput, $processedContext, ['messages']);

        $this->fileProcessor($docIdentifier, $xmlName);

        $this->expectedEmailQueueSideEffect(SendEbsrErrorsCmd::class, ['id' => $ebsrSubId], $ebsrSubId, new Result());

        $this->sut->handleCommand($command);
    }

    /**
     * Common assertions for ebsr submission entity
     *
     * @param $ebsrSubId
     * @param $organisation
     * @param $document
     *
     * @return m\MockInterface
     */
    private function failedEbsrSubmission($ebsrSubId, $organisation, $document)
    {
        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);

        $ebsrSubmission->shouldReceive('getId')->andReturn($ebsrSubId);
        $ebsrSubmission->shouldReceive('getOrganisation')->once()->andReturn($organisation);
        $ebsrSubmission->shouldReceive('getDocument')->once()->andReturn($document);

        $ebsrSubmission->shouldReceive('beginValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::VALIDATING_STATUS])
            ->andReturnSelf();

        $ebsrSubmission->shouldReceive('finishValidating')
            ->once()
            ->with($this->refData[EbsrSubmissionEntity::FAILED_STATUS], 'json string')
            ->andReturnSelf();

        return $ebsrSubmission;
    }

    /**
     * Common assertions for ebsr submission repo
     *
     * @param $command
     * @param $ebsrSubmission
     * @param $timesSaved
     */
    private function ebsrSubmissionRepo($command, $ebsrSubmission, $timesSaved)
    {
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($ebsrSubmission);

        $this->repoMap['EbsrSubmission']->shouldReceive('save')
            ->times($timesSaved)
            ->with(m::type(EbsrSubmissionEntity::class));
    }

    /**
     * Common assertions for document entity
     *
     * @param $docIdentifier
     * @param $documentDescription
     *
     * @return m\MockInterface
     */
    private function basicDocument($docIdentifier, $documentDescription)
    {
        $document = m::mock(DocumentEntity::class);
        $document->shouldReceive('getIdentifier')->once()->andReturn($docIdentifier);
        $document->shouldReceive('getDescription')->once()->andReturn($documentDescription);

        return $document;
    }

    /**
     * Common assertions for file processor
     *
     * @param $docIdentifier
     * @param $xmlName
     */
    private function fileProcessor($docIdentifier, $xmlName)
    {
        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('setSubDirPath')
            ->with('tmp/directory/path')
            ->once();
        $this->mockedSmServices[FileProcessorInterface::class]
            ->shouldReceive('fetchXmlFileNameFromDocumentStore')
            ->with($docIdentifier)
            ->once()
            ->andReturn($xmlName);
    }

    /**
     * Common assertions for traffic areas
     *
     * @param $parsedTrafficAreas
     * @param $trafficArea1
     * @param $trafficArea2
     * @param $trafficArea3
     *
     * @return ArrayCollection
     */
    private function trafficAreas($parsedTrafficAreas, $trafficArea1, $trafficArea2, $trafficArea3)
    {
        $trafficAreas = [
            0 => $trafficArea1
        ];

        $this->repoMap['TrafficArea']->shouldReceive('fetchByTxcName')
            ->once()
            ->with($parsedTrafficAreas)
            ->andReturn($trafficAreas);

        //includes traffic areas from the local authorities
        return new ArrayCollection([$trafficArea1, $trafficArea2, $trafficArea3]);
    }

    /**
     * Common assertions for local authorities
     *
     * @param $parsedLocalAuthorities
     * @param $firstTa
     * @param $secondTa
     *
     * @return ArrayCollection
     */
    private function localAuthorities($parsedLocalAuthorities, $firstTa, $secondTa)
    {
        $localAuthority1 = m::mock(LocalAuthorityEntity::class);
        $localAuthority1->shouldReceive('getTrafficArea')->andReturn($firstTa);
        $localAuthority2 = m::mock(LocalAuthorityEntity::class);
        $localAuthority2->shouldReceive('getTrafficArea')->andReturn($secondTa);

        $localAuthorities = [
            0 => $localAuthority1,
            1 => $localAuthority2,
        ];

        $this->repoMap['LocalAuthority']->shouldReceive('fetchByTxcName')
            ->once()
            ->with($parsedLocalAuthorities)
            ->andReturn($localAuthorities);

        return new ArrayCollection([$localAuthority1, $localAuthority2]);
    }

    /**
     * Common assertions for bus service types
     *
     * @param array $busServiceTypes
     *
     * @return ArrayCollection
     */
    private function busServiceTypes($busServiceTypes)
    {
        $busServiceTypeKeys = array_keys($busServiceTypes);

        $busServiceType1 = m::mock(BusServiceTypeEntity::class);
        $busServiceType2 = m::mock(BusServiceTypeEntity::class);

        $busServiceTypeResult = [
            0 => $busServiceType1,
            1 => $busServiceType2
        ];

        $this->repoMap['BusServiceType']->shouldReceive('fetchByTxcName')
            ->once()
            ->with($busServiceTypeKeys)
            ->andReturn($busServiceTypeResult);

        return new ArrayCollection([$busServiceType1, $busServiceType2]);
    }

    /**
     * Common assertions for naptan codes
     *
     * @param $naptanCodes
     *
     * @return ArrayCollection
     */
    private function naptan($naptanCodes)
    {
        $naptanAuthority1 = m::mock(LocalAuthorityEntity::class);
        $naptanAuthority2 = m::mock(LocalAuthorityEntity::class);

        $naptanAuthorities = [
            0 => $naptanAuthority1,
            1 => $naptanAuthority2,
        ];

        $this->repoMap['LocalAuthority']->shouldReceive('fetchByNaptan')
            ->once()
            ->with($naptanCodes)
            ->andReturn($naptanAuthorities);

        return new ArrayCollection([$naptanAuthority1, $naptanAuthority2]);
    }

    /**
     * Common assertions to mock input filter
     *
     * @param $inputName
     * @param $inputValue
     * @param $context
     * @param $outputValue
     */
    private function mockInput($inputName, $inputValue, $context, $outputValue)
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
            ->andReturn(true);
        $this->mockedSmServices[$inputName]
            ->shouldReceive('getValue')
            ->once()
            ->andReturn($outputValue);
    }

    /**
     * Common assertions to mock failed input filter
     *
     * @param $inputName
     * @param $inputValue
     * @param $context
     * @param $messages
     */
    private function mockInputFailure($inputName, $inputValue, $context, $messages)
    {
        $this->mockedSmServices[$inputName]
            ->shouldReceive('setValue')
            ->once()
            ->with($inputValue)
            ->andReturnSelf();
        $this->mockedSmServices[$inputName]
            ->shouldReceive('isValid')
            ->with($context)
            ->once()
            ->andReturn(false);
        $this->mockedSmServices[$inputName]
            ->shouldReceive('getValue')
            ->once()
            ->andReturn($inputValue);
        $this->mockedSmServices[$inputName]
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn($messages);
    }

    /**
     * Common assertions to mock side effects when submission is successful
     *
     * @param $existingRegNo
     * @param $savedBusRegId
     * @param $licenceId
     * @param $documentId
     * @param $taskMessage
     */
    private function successSideEffects($existingRegNo, $savedBusRegId, $licenceId, $documentId, $taskMessage, $fee)
    {
        $this->taskSideEffect($existingRegNo, $savedBusRegId, $licenceId, $taskMessage);
        $this->documentLinkSideEffect($documentId, $savedBusRegId, $licenceId);
        $this->requestMapSideEffect($savedBusRegId);
        $this->txcInboxSideEffect($savedBusRegId);

        if ($fee) {
            $this->busFeeSideEffect($savedBusRegId);
        }
    }

    /**
     * Common assertions for task side effect
     *
     * @param $existingRegNo
     * @param $savedBusRegId
     * @param $licenceId
     * @param $taskMessage
     */
    private function taskSideEffect($existingRegNo, $savedBusRegId, $licenceId, $taskMessage)
    {
        $taskData = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => $taskMessage . $existingRegNo,
            'actionDate' => date('Y-m-d'),
            'busReg' => $savedBusRegId,
            'licence' => $licenceId,
        ];

        $this->expectedSideEffect(CreateTaskCmd::class, $taskData, new Result());
    }

    /**
     * Common assertions for side effect updating document links
     *
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
     * Common assertions for a request map side effect
     *
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
     * Common assertions for bus fee side effect
     *
     * @param $savedBusRegId
     */
    private function busFeeSideEffect($savedBusRegId)
    {
        $this->expectedSideEffect(CreateBusFeeCmd::class, ['id' => $savedBusRegId], new Result());
    }

    /**
     * Common assertions for txc inbox side effect
     *
     * @param $savedBusRegId
     */
    private function txcInboxSideEffect($savedBusRegId)
    {
        $this->expectedSideEffect(CreateTxcInboxCmd::class, ['id' => $savedBusRegId], new Result());
    }
}
