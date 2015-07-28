<?php

/**
 * Queue Complete Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue\Complete;
use Dvsa\Olcs\Api\Domain\Repository\Queue as Repo;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 *  Queue Complete Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Complete();
        $this->mockRepo('Queue', Repo::class);

        $this->refData = [
            QueueEntity::STATUS_COMPLETE,
        ];

        parent::setUp();
    }

    /**
     * Test handleCommand method
     */
    public function testHandleCommand()
    {
        $item = new QueueEntity();
        $command = Cmd::create(['item' => $item]);

        $this->repoMap['Queue']
            ->shouldReceive('save')
            ->once()
            ->with($item);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Queue item marked complete'], $result->getMessages());

        $this->assertEquals(QueueEntity::STATUS_COMPLETE, $item->getStatus()->getId());
    }
}
