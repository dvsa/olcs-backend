<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue\Failed;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Queue\Failed
 */
class FailedTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Failed();
        $this->mockRepo('Queue', Repository\Queue::class);

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
        $lastErr = 'unit_LastErr';

        $mockQueueEntity = new QueueEntity();
        $mockQueueEntity->setId($id);

        $command = DomainCmd\Queue\Failed::create(
            [
                'item' => $mockQueueEntity,
                'lastError' => $lastErr,
            ]
        );

        $this->repoMap['Queue']
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (QueueEntity $entity) use ($id, $lastErr) {
                    static::assertEquals($id, $entity->getId());
                    static::assertEquals($lastErr, $entity->getLastError());
                    static::assertEquals(QueueEntity::STATUS_FAILED, $entity->getStatus()->getId());
                }
            );

        $this->expectedSideEffect(DomainCmd\Queue\Delete::class, ['id' => $id], new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Queue item marked failed'], $result->getMessages());

        $this->assertEquals(QueueEntity::STATUS_FAILED, $mockQueueEntity->getStatus()->getId());
    }
}
