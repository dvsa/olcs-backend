<?php

/**
 * Update Sla Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Pi\UpdateSla;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\System\Sla as SlaEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Pi\UpdateSla as Cmd;
use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate as GenerateSlaTargetDateCmd;

/**
 * Update Sla Test
 */
class UpdateSlaTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateSla();
        $this->mockRepo('Pi', PiRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            SlaEntity::VERBAL_DECISION_ONLY,
            SlaEntity::WRITTEN_OUTCOME_DECISION,
            SlaEntity::WRITTEN_OUTCOME_REASON
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider writtenOutcomeProvider
     *
     * @param string|null $writtenOutcome
     */
    public function testHandleCommand($writtenOutcome)
    {
        $id = 11;
        $version = 22;

        $command = Cmd::Create(
            [
                'id' => $id,
                'version' => $version,
                'writtenOutcome' => $writtenOutcome
            ]
        );

        /** @var PiEntity $pi */
        $pi = m::mock(PiEntity::class)->makePartial();
        $pi->setId($id);
        $pi->shouldReceive('isClosed')->once()->andReturn(false);

        $this->repoMap['Pi']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($pi)
            ->shouldReceive('save')
            ->with(m::type(PiEntity::class))
            ->once();

        $this->expectedSideEffect(
            GenerateSlaTargetDateCmd::class,
            [
                'pi' => $id
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @return array
     */
    public function writtenOutcomeProvider()
    {
        return [
            [SlaEntity::VERBAL_DECISION_ONLY],
            [SlaEntity::WRITTEN_OUTCOME_DECISION],
            [SlaEntity::WRITTEN_OUTCOME_REASON],
            [null]
        ];
    }
}
