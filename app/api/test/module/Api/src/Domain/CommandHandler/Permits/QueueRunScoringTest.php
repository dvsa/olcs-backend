<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\QueueRunScoring;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class QueueRunScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);

        $this->sut = m::mock(QueueRunScoring::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermitStockEntity::STATUS_SCORING_PENDING
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $stockId = 47;
        $deviation = 1.5;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);
        $command->shouldReceive('getDeviation')
            ->andReturn($deviation);

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(QueueRunScoringPermitted::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => true,
                    'message' => 'ok'
                ];
            });

        $this->expectedQueueSideEffect($stockId, Queue::TYPE_RUN_ECMT_SCORING, ['deviation' => 1.5]);

        $stock = m::mock(IrhpPermitStockEntity::class);
        $stock->shouldReceive('proceedToScoringPending')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_SCORING_PENDING])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($stock)
            ->once()
            ->ordered()
            ->globally();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPermittedQueryFailed()
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(QueueRunScoringPermitted::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => false,
                    'message' => 'prerequisites failed'
                ];
            });

        $this->sut->handleCommand($command);
    }
}
