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

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermitStockEntity::STATUS_ACCEPT_PENDING
        ];

        parent::initReferences();
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

        $stock = m::mock(IrhpPermitStockEntity::class);
        $stock->shouldReceive('proceedToAcceptPending')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_ACCEPT_PENDING])
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

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Queueing accept scoring of ECMT applications'],
            $result->getMessages()
        );
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
