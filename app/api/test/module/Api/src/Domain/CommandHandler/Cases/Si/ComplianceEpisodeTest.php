<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\ComplianceEpisode;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiCategory as SiCategoryRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiCategoryType as SiCategoryTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyImposedType as SiPenaltyImposedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyRequestedType as SiPenaltyRequestedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\ErruRequest as ErruRequestRepo;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategoryType as SiCategoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyImposedType as SiPenaltyImposedTypeEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyRequestedType as SiPenaltyRequestedTypeEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategory as SiCategoryEntity;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\XmlStructureInputFactory;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\SeriousInfringementInputFactory;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory;
use Dvsa\Olcs\Transfer\Command\Cases\Si\ComplianceEpisode as ComplianceEpisodeCmd;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * ComplianceEpisodeTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ComplianceEpisodeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ComplianceEpisode();
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('SeriousInfringement', SiRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('Country', CountryRepo::class);
        $this->mockRepo('SiCategory', SiCategoryRepo::class);
        $this->mockRepo('SiCategoryType', SiCategoryTypeRepo::class);
        $this->mockRepo('SiPenaltyImposedType', SiPenaltyImposedTypeRepo::class);
        $this->mockRepo('SiPenaltyRequestedType', SiPenaltyRequestedTypeRepo::class);
        $this->mockRepo('ErruRequest', ErruRequestRepo::class);

        $this->mockedSmServices = [
            'ComplianceXmlStructure' => m::mock(XmlStructureInputFactory::class),
            'ComplianceEpisodeInput' => m::mock(ComplianceEpisodeInputFactory::class),
            'SeriousInfringementInput' => m::mock(SeriousInfringementInputFactory::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CasesEntity::LICENCE_CASE_TYPE,
            ErruRequestEntity::DEFAULT_CASE_TYPE,
            CasesEntity::ERRU_DEFAULT_CASE_CATEGORY,
            'pen_erru_imposed_executed_yes'
        ];

        $this->references = [
            SiPenaltyImposedTypeEntity::class => [
                102 => m::mock(SiPenaltyImposedTypeEntity::class)
            ],
            SiPenaltyRequestedTypeEntity::class => [
                301 => m::mock(SiPenaltyRequestedTypeEntity::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * Tests processing the XML and creating the serious infringement
     */
    public function testHandleCommand()
    {
        $xmlString = 'xml string';
        $command = ComplianceEpisodeCmd::create(['xml' => $xmlString]);
        $licenceId = 999;

        //common data
        $workflowId = '20776dc3-5fe7-42d5-b554-09ad12fa25c4';
        $notificationNumber = '0ffefb6b-6344-4a60-9a53-4381c32f98d9';
        $originatingAuthority = 'originating authority';
        $licenceNumber = 'OB1234567';
        $vrm = 'ABC123';
        $transportUndertakingName = 'transport undertaking';
        $memberStateCode = 'PL';

        //imposed erru
        $siPenaltyImposedType = 102;
        $startDate = '2016-03-14';
        $endDate = '2016-04-14';
        $finalDecisionDate = '2016-02-14';
        $executed = 'Yes';
        $executedRefData = 'pen_erru_imposed_executed_yes';

        $imposedErru = [
            'finalDecisionDate' => $finalDecisionDate,
            'siPenaltyImposedType' => $siPenaltyImposedType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'executed' => $executed
        ];

        $filteredImposedErru = [
            'finalDecisionDate' => new \DateTime($finalDecisionDate . ' 00:00:00'),
            'siPenaltyImposedType' => $siPenaltyImposedType,
            'startDate' => new \DateTime($startDate . ' 00:00:00'),
            'endDate' => new \DateTime($endDate . ' 00:00:00'),
            'executed' => $executedRefData
        ];

        //requested erru
        $siPenaltyRequestedType = 301;
        $duration = 12;

        $requestedErru = [
            'siPenaltyRequestedType' => $siPenaltyRequestedType,
            'duration' => $duration
        ];

        //serious infringement
        $infringementDate = '2015-12-25';
        $checkDate = '2015-12-24';
        $siCategoryType = 101;

        $si = [
            'infringementDate' => $infringementDate,
            'siCategoryType' => $siCategoryType,
            'checkDate' => $checkDate,
            'imposedErrus' => [
                0 => $imposedErru
            ],
            'requestedErrus' => [
                0 => $requestedErru
            ]
        ];

        $filteredSi = [
            'infringementDate' => new \DateTime($infringementDate . ' 00:00:00'),
            'siCategoryType' => $siCategoryType,
            'checkDate' => new \DateTime($checkDate . ' 00:00:00'),
            'imposedErrus' => [
                0 => $filteredImposedErru
            ],
            'requestedErrus' => [
                0 => $requestedErru
            ]
        ];

        $xmlDomDocument = new \DomDocument();

        $erruData = [
            'workflowId' => $workflowId,
            'memberStateCode' => $memberStateCode,
            'notificationNumber' => $notificationNumber,
            'originatingAuthority' => $originatingAuthority,
            'licenceNumber' => $licenceNumber,
            'vrm' => $vrm,
            'transportUndertakingName' => $transportUndertakingName,
            'si' => [
                0 => $si
            ]
        ];

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('setValue')
            ->with($xmlString)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('getValue')
            ->andReturn($xmlDomDocument);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('setValue')
            ->with($xmlDomDocument)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('getValue')
            ->andReturn($erruData);

        $this->mockedSmServices['SeriousInfringementInput']
            ->shouldReceive('setValue')
            ->with($si)
            ->andReturnSelf();

        $this->mockedSmServices['SeriousInfringementInput']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['SeriousInfringementInput']
            ->shouldReceive('getValue')
            ->andReturn($filteredSi);

        $licenceEntity = m::mock(LicenceEntity::class);
        $licenceEntity->shouldReceive('getId')->once()->andReturn($licenceId);

        $this->repoMap['Licence']
            ->shouldReceive('fetchByLicNoWithoutAdditionalData')
            ->once()
            ->with($licenceNumber)
            ->andReturn($licenceEntity);

        $countryEntity = m::mock(CountryEntity::class);

        $this->repoMap['Country']
            ->shouldReceive('fetchById')
            ->once()
            ->with($memberStateCode)
            ->andReturn($countryEntity);

        $siCategoryTypeEntity = m::mock(SiCategoryTypeEntity::class);

        $this->repoMap['SiCategoryType']
            ->shouldReceive('fetchById')
            ->once()
            ->with($siCategoryType)
            ->andReturn($siCategoryTypeEntity);

        $siCategoryEntity = m::mock(SiCategoryEntity::class);

        $this->repoMap['SiCategory']
            ->shouldReceive('fetchById')
            ->once()
            ->with(SiCategoryEntity::ERRU_DEFAULT_CATEGORY)
            ->andReturn($siCategoryEntity);

        $siPenaltyImposedTypeEntity = m::mock(SiPenaltyImposedTypeEntity::class);

        $this->repoMap['SiPenaltyImposedType']
            ->shouldReceive('fetchById')
            ->once()
            ->with($siPenaltyImposedType)
            ->andReturn($siPenaltyImposedTypeEntity);

        $siPenaltyRequestedTypeEntity = m::mock(SiPenaltyRequestedTypeEntity::class);

        $this->repoMap['SiPenaltyRequestedType']
            ->shouldReceive('fetchById')
            ->once()
            ->with($siPenaltyRequestedType)
            ->andReturn($siPenaltyRequestedTypeEntity);

        $this->repoMap['ErruRequest']
            ->shouldReceive('existsByWorkflowId')
            ->once()
            ->with($workflowId)
            ->andReturn(false);

        $this->repoMap['Cases']->shouldReceive('save')->once()->with(m::type(CasesEntity::class));

        $taskResult = new Result();
        $taskResult->addId('task', 88);
        $taskData = [
            'category' => TaskEntity::CATEGORY_NR,
            'subCategory' => TaskEntity::SUBCATEGORY_NR,
            'description' => 'ERRU case has been automatically created',
            'actionDate' => date('Y-m-d', strtotime('+7 days')),
            'urgent' => 'Y',
            'case' => null,
            'licence' => $licenceId,
        ];
        $this->expectedSideEffect(CreateTask::class, $taskData, $taskResult);

        $result = $this->sut->handleCommand($command);
        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\Exception
     */
    public function testExceptionThrownForMissingData()
    {
        $xmlString = 'xml string';
        $command = ComplianceEpisodeCmd::create(['xml' => $xmlString]);

        $licenceNumber = 'OB1234567';
        $workflowId = '0ffefb6b-6344-4a60-9a53-4381c32f98d9';

        $xmlDomDocument = new \DomDocument();

        $erruData = [
            'licenceNumber' => $licenceNumber,
            'workflowId' => $workflowId
        ];

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('setValue')
            ->with($xmlString)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('getValue')
            ->andReturn($xmlDomDocument);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('setValue')
            ->with($xmlDomDocument)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('getValue')
            ->andReturn($erruData);

        $this->repoMap['Licence']
            ->shouldReceive('fetchByLicNoWithoutAdditionalData')
            ->once()
            ->with($licenceNumber)
            ->andThrowExceptions([new NotFoundException()]);

        $this->repoMap['ErruRequest']
            ->shouldReceive('existsByWorkflowId')
            ->once()
            ->with($workflowId)
            ->andReturn(false);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\Exception
     */
    public function testExceptionThrownForExistingErruRequest()
    {
        $xmlString = 'xml string';
        $command = ComplianceEpisodeCmd::create(['xml' => $xmlString]);

        $licenceNumber = 'OB1234567';
        $workflowId = '0ffefb6b-6344-4a60-9a53-4381c32f98d9';

        $xmlDomDocument = new \DomDocument();

        $erruData = [
            'licenceNumber' => $licenceNumber,
            'workflowId' => $workflowId
        ];

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('setValue')
            ->with($xmlString)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('getValue')
            ->andReturn($xmlDomDocument);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('setValue')
            ->with($xmlDomDocument)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(true);

        $this->mockedSmServices['ComplianceEpisodeInput']
            ->shouldReceive('getValue')
            ->andReturn($erruData);

        $this->repoMap['ErruRequest']
            ->shouldReceive('existsByWorkflowId')
            ->once()
            ->with($workflowId)
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\Exception
     */
    public function testExceptionThrownForValidationFailure()
    {
        $xmlString = 'xml string';
        $command = ComplianceEpisodeCmd::create(['xml' => $xmlString]);

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('setValue')
            ->with($xmlString)
            ->andReturnSelf();

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('isValid')
            ->with([])
            ->andReturn(false);

        $this->mockedSmServices['ComplianceXmlStructure']
            ->shouldReceive('getMessages')
            ->andReturn(['message 1', 'message2']);

        $this->sut->handleCommand($command);
    }
}
