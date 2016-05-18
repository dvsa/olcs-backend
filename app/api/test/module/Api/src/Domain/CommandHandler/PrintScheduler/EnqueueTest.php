<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\Enqueue as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * EnqueueTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EnqueueTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);
        $this->mockRepo('User', \Dvsa\Olcs\Api\Domain\Repository\User::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class)->makePartial();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
        ];

        parent::initReferences();
    }

    public function testHandleCommandMissingDocumentIdParam()
    {
        $command = Cmd::create([]);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandDocumentIdParamNotNumber()
    {
        $command = Cmd::create(['documentId' => 'x']);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandUserWithNoTeamPrinter()
    {
        $command = Cmd::create(['documentId' => 200116]);

        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $user->setTeam($team);
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->setExpectedException(
            \Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class,
            'Failed to generate document as there are no printer settings for the current user'
        );
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandUserWithNoTeam()
    {
        $command = Cmd::create(['documentId' => 200116, 'jobName' => 'JOBNAME']);

        $user = new \Dvsa\Olcs\Api\Entity\User\User('PID', 'TYPE');
        $user->setId(10);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->expectCreateQueue();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            ["Document id '200116', 'JOBNAME' queued for print"],
            $result->getMessages()
        );
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['documentId' => 200116, 'jobName' => 'JOBNAME', 'user' => 10]);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $team->addTeamPrinters('PRINTER 1');
        $user = new \Dvsa\Olcs\Api\Entity\User\User('PID', 'TYPE');
        $user->setTeam($team);
        $user->setId(10);
        $this->repoMap['User']->shouldReceive('fetchById')
            ->with(10)
            ->andReturn($user)
            ->getMock();

        $this->expectCreateQueue();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            ["Document id '200116', 'JOBNAME' queued for print"],
            $result->getMessages()
        );
    }

    private function expectCreateQueue()
    {
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Create::class,
            [
                'entityId' => 200116,
                'type' => Queue::TYPE_PRINT,
                'status' => Queue::STATUS_QUEUED,
                'options' => json_encode(
                    [
                        'userId' => 10,
                        'jobName' => 'JOBNAME'
                    ]
                ),
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );
    }
}
