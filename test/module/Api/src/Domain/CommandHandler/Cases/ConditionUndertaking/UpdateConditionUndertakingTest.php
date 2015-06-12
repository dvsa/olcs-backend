<?php

/**
 * Update ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking\UpdateConditionUndertaking;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\OperatingCentre as OperatingCentreRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\ConditionUndertaking\UpdateConditionUndertaking as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;

/**
 * Update ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateConditionUndertakingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateConditionUndertaking();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('OperatingCentre', OperatingCentreRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'cat_oc',
            'cat_lic',
            'cdt_und',
            'cdt_con'
        ];

        $this->references = [
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ],
            OperatingCentreEntity::class => [
                16 => m::mock(OperatingCentreEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandAttachedToOc()
    {
        $command = Cmd::create(
            [
                'id' => 99,
                'version' => 1,
                'conditionType' => 'cdt_und',
                'notes' => 'Notes',
                'isFulfilled' => 'N',
                'attachedTo' => 'cat_oc',
                'operatingCentre' => 16
            ]
        );

        /** @var OperatingCentreEntity $operatingCentreEntity */
        $operatingCentreEntity = m::mock(OperatingCentreEntity::class)->makePartial();
        $operatingCentreEntity->setId(16);

        /** @var LicenceEntity $licenceEntity */
        $licenceEntity = m::mock(LicenceEntity::class)->makePartial();
        $licenceEntity->setId(7);

        /** @var CasesEntity $caseEntity */
        $caseEntity = m::mock(CasesEntity::class)->makePartial();
        $caseEntity->setLicence($licenceEntity);

        $this->repoMap['OperatingCentre']
            ->shouldReceive('fetchById')
            ->with(16)
            ->andReturn($operatingCentreEntity);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licenceEntity);

        $this->repoMap['Cases']
            ->shouldReceive('fetchById')
            ->with(24)
            ->andReturn($caseEntity);

        /** @var ConditionUndertakingEntity $conditionUndertakingEntity */
        $conditionUndertakingEntity = m::mock(ConditionUndertakingEntity::class)->makePartial();
        $conditionUndertakingEntity->setId($command->getId());
        $conditionUndertakingEntity->setVersion($command->getVersion());

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($conditionUndertakingEntity)
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ConditionUndertakingEntity::class))
            ->andReturn($conditionUndertakingEntity)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('ConditionUndertaking updated', $result->getMessages());
    }

    public function testHandleCommandAttachedToLicence()
    {
        $command = Cmd::create(
            [
                'id' => 99,
                'version' => 1,
                'conditionType' => 'cdt_con',
                'notes' => 'Notes',
                'isFulfilled' => 'N',
                'attachedTo' => 'cat_lic'
            ]
        );

        /** @var OperatingCentreEntity $operatingCentreEntity */
        $operatingCentreEntity = m::mock(OperatingCentreEntity::class)->makePartial();
        $operatingCentreEntity->setId(16);

        /** @var LicenceEntity $licenceEntity */
        $licenceEntity = m::mock(LicenceEntity::class)->makePartial();
        $licenceEntity->setId(7);

        /** @var CasesEntity $caseEntity */
        $caseEntity = m::mock(CasesEntity::class)->makePartial();
        $caseEntity->setLicence($licenceEntity);

        $this->repoMap['OperatingCentre']
            ->shouldReceive('fetchById')
            ->with(16)
            ->andReturn($operatingCentreEntity);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licenceEntity);

        $this->repoMap['Cases']
            ->shouldReceive('fetchById')
            ->with(24)
            ->andReturn($caseEntity);

        /** @var ConditionUndertakingEntity $conditionUndertakingEntity */
        $conditionUndertakingEntity = m::mock(ConditionUndertakingEntity::class)->makePartial();
        $conditionUndertakingEntity->setId($command->getId());
        $conditionUndertakingEntity->setVersion($command->getVersion());

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($conditionUndertakingEntity)
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ConditionUndertakingEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('ConditionUndertaking updated', $result->getMessages());
    }
}
