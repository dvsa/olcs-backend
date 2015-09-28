<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Queue as CommandHandler;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Queue as Command;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;

/**
 * Queue letters test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class QueueTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $this->mockAuthService();

        $data = [
            'ids' => [1],
            'type' => QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER
        ];
        $command = Command::create($data);

        $queueLettersResult = new Result();
        $queueLettersResult->addId('queue1', 1);
        $queueLettersResult->addMessage('Queue created');

        $params = [
            'entityId' => 1,
            'type' => QueueEntity::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER,
            'status' => QueueEntity::STATUS_QUEUED,
            'user' => 1
        ];
        $this->expectedSideEffect(CreateQueueCmd::class, $params, $queueLettersResult);

        $result = $this->sut->handleCommand($command);
        $messages = [
            'Queue created',
            'All letters queued'
        ];
        $this->assertEquals($messages, $result->getMessages());
        $this->assertEquals(['queue1' => 1], $result->getIds());
    }

    protected function mockAuthService()
    {
        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }
}
