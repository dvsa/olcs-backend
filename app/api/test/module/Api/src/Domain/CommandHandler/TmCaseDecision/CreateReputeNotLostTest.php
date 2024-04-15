<?php

/**
 * Create ReputeNotLost Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TmCaseDecision;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TmCaseDecision\CreateReputeNotLost;
use Dvsa\Olcs\Api\Domain\Repository\TmCaseDecision;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TmCaseDecision as TmCaseDecisionEntity;
use Dvsa\Olcs\Transfer\Command\TmCaseDecision\CreateReputeNotLost as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCommandHandlerTestCase;

/**
 * Create ReputeNotLost Test
 */
class CreateReputeNotLostTest extends AbstractCommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateReputeNotLost();
        $this->mockRepo('TmCaseDecision', TmCaseDecision::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TmCaseDecisionEntity::DECISION_REPUTE_NOT_LOST,
        ];

        $this->references = [
            CasesEntity::class => [
                11 => m::mock(CasesEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'case' => 11,
            'isMsi' => 'Y',
            'decisionDate' => '2016-01-01',
            'notifiedDate' => '2016-01-01',
            'reputeNotLostReason' => 'testing',
        ];

        $command = Cmd::create($data);

        $this->repoMap['TmCaseDecision']->shouldReceive('save')
            ->once()
            ->with(m::type(TmCaseDecisionEntity::class))
            ->andReturnUsing(
                function (TmCaseDecisionEntity $entity) {
                    $entity->setId(111);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'tmCaseDecision' => 111,
            ],
            'messages' => [
                'Decision created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
