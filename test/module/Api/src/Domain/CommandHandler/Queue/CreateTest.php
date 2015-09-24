<?php

/**
 * Queue Create Command Handler Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue\Create;
use Dvsa\Olcs\Api\Domain\Repository\Queue as Repo;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * Queue Create Command Handler Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Create();
        $this->mockRepo('Queue', Repo::class);

        $this->refData = [
            QueueEntity::STATUS_QUEUED,
            QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER
        ];
        $this->references = [
            UserEntity::class => [
                2 => m::mock(UserEntity::class)
            ]
        ];

        parent::setUp();
    }

    /**
     * Test handleCommand method
     */
    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'status' => QueueEntity::STATUS_QUEUED,
                'type' => QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER,
                'entityId' => 1,
                'user' => 2
            ]
        );

        $savedQueue = null;
        $this->repoMap['Queue']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(QueueEntity::class))
            ->andReturnUsing(
                function (QueueEntity $queue) use (&$savedQueue) {
                    $queue->setId(1);
                    $queue->setCreatedBy($this->references[UserEntity::class][2]);
                    $queue->setType($this->refData[QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER]);
                    $queue->setStatus($this->refData[QueueEntity::STATUS_QUEUED]);
                    $savedQueue = $queue;
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Queue created'], $result->getMessages());
        $this->assertEquals(1, $result->getId('queue1'));
        $this->assertEquals($savedQueue->getId(), 1);
        $this->assertEquals(
            $savedQueue->getType(),
            $this->refData[QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER]
        );
        $this->assertEquals(
            $savedQueue->getStatus(),
            $this->refData[QueueEntity::STATUS_QUEUED]
        );
        $this->assertEquals(
            $savedQueue->getCreatedBy(),
            $this->references[UserEntity::class][2]
        );
    }
}
