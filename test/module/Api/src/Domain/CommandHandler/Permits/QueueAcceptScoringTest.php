<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\QueueAcceptScoring;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class QueueAcceptScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);

        $this->sut = m::mock(QueueAcceptScoring::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(QueueAcceptScoringPermitted::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => true,
                    'message' => 'ok'
                ];
            });

        $this->expectedQueueSideEffect($stockId, Queue::TYPE_ACCEPT_ECMT_SCORING, []);

        $this->repoMap['IrhpPermitStock']->shouldReceive('updateStatus')
            ->with($stockId, IrhpPermitStockEntity::STATUS_ACCEPT_PENDING)
            ->once();

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
                $this->assertInstanceOf(QueueAcceptScoringPermitted::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => false,
                    'message' => 'prerequisites failed'
                ];
            });

        $this->sut->handleCommand($command);
    }
}
