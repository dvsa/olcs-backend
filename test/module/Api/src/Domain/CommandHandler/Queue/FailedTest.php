<?php

/**
 * Queue Failed Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue\Failed;
use Dvsa\Olcs\Api\Domain\Repository\Queue as Repo;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Queue\Delete as DeleteQueueCmd;

/**
 *  Queue Failed Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FailedTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Failed();
        $this->mockRepo('Queue', Repo::class);

        $this->refData = [
            QueueEntity::STATUS_FAILED,
        ];

        parent::setUp();
    }

    /**
     * Test handleCommand method
     */
    public function testHandleCommand()
    {
        $id = 1234;

        $item = new QueueEntity();
        $item->setId($id);
        $command = Cmd::create(['item' => $item]);

        $this->repoMap['Queue']
            ->shouldReceive('save')
            ->once()
            ->with($item);

        $this->expectedSideEffect(DeleteQueueCmd::class, ['id' => $id], new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Queue item marked failed'], $result->getMessages());

        $this->assertEquals(QueueEntity::STATUS_FAILED, $item->getStatus()->getId());
    }
}
