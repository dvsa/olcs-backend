<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreatQueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\PrintPermits as PrintPermitsHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\Permits\PrintPermits as PrintPermitsCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Print Permits Test
 */
class PrintPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintPermitsHandler();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);
        $this->mockRepo('Queue', QueueRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            'Config' => [
                'permit_printing' => ['max_batch_size' => null]
            ],
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermit::STATUS_AWAITING_PRINTING,
        ];

        parent::initReferences();
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage ERR_PERMIT_PRINTING_MAX_BATCH_SIZE_REACHED
     */
    public function testHandleMaxBatchSizeReached()
    {
        $cmdData = [
            'ids' => range(1, PrintPermitsHandler::MAX_BATCH_SIZE+1),
        ];

        $command = PrintPermitsCmd::create($cmdData);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage ERR_PERMIT_PRINTING_ALREADY_IN_PROGRESS
     */
    public function testHandlePrintingAlreadyInProgress()
    {
        $cmdData = [
            'ids' => range(1, PrintPermitsHandler::MAX_BATCH_SIZE),
        ];

        $command = PrintPermitsCmd::create($cmdData);

        $this->repoMap['Queue']
            ->shouldReceive('isItemInQueue')
            ->with(
                [Queue::TYPE_PERMIT_GENERATE, Queue::TYPE_PERMIT_PRINT],
                [Queue::STATUS_QUEUED, Queue::STATUS_PROCESSING]
            )
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $userId = 1;
        $p1Id = 10;
        $p2Id = 11;

        $cmdData = [
            'ids' => [$p1Id, $p2Id],
        ];

        $command = PrintPermitsCmd::create($cmdData);

        $this->repoMap['Queue']
            ->shouldReceive('isItemInQueue')
            ->with(
                [Queue::TYPE_PERMIT_GENERATE, Queue::TYPE_PERMIT_PRINT],
                [Queue::STATUS_QUEUED, Queue::STATUS_PROCESSING]
            )
            ->andReturn(false);

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId($userId);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $data = [
            'type' => Queue::TYPE_PERMIT_GENERATE,
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode(
                [
                    'ids' => [$p1Id, $p2Id],
                    'user' => $userId
                ]
            )
        ];
        $this->expectedSideEffect(
            CreatQueue::class,
            $data,
            (new Result())->addMessage('Queue item created')
        );

        $p1 = m::mock(PermitWindowEntity::class);
        $p1->shouldReceive('getId')
            ->andReturn($p1Id)
            ->shouldReceive('proceedToAwaitingPrinting')
            ->with($this->refData[IrhpPermit::STATUS_AWAITING_PRINTING])
            ->once();

        $p2 = m::mock(PermitWindowEntity::class);
        $p2->shouldReceive('getId')
            ->andReturn($p2Id)
            ->shouldReceive('proceedToAwaitingPrinting')
            ->with($this->refData[IrhpPermit::STATUS_AWAITING_PRINTING])
            ->once();

        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchByIds')
            ->with([$p1Id, $p2Id])
            ->andReturn([$p1, $p2])
            ->shouldReceive('save')
            ->with($p1)
            ->once()
            ->shouldReceive('save')
            ->with($p2)
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'id' => [$p1Id, $p2Id]
            ],
            'messages' => [
                'Queue item created',
                'Permits submitted for printing',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
