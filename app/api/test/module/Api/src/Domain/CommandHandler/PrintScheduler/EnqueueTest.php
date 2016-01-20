<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\Enqueue as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * CreateSeparatorSheetTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EnqueueTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);

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

    public function testHandleCommandUserWithNoTeam()
    {
        $this->markTestIncomplete('This is a temporary stub. Final implemention todo');

        $user = new \Dvsa\Olcs\Api\Entity\User\User('PID', 'TYPE');
        $user->setId(10);

        $command = Cmd::create(['documentId' => 200116]);

        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandUserNoPrinters()
    {
        $command = Cmd::create(['documentId' => 200116]);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $user = new \Dvsa\Olcs\Api\Entity\User\User('PID', 'TYPE');
        $user->setTeam($team);
        $user->setId(10);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['documentId' => 200116, 'jobName' => 'JOBNAME']);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $team->addPrinters('PRINTER 1');
        $user = new \Dvsa\Olcs\Api\Entity\User\User('PID', 'TYPE');
        $user->setTeam($team);
        $user->setId(10);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Create::class,
            [
                'entityId' => 200116,
                'type' => Queue::TYPE_PRINT,
                'status' => Queue::STATUS_QUEUED,
                'user' => 10,
                'options' => json_encode(
                    [
                        'userId' => 10,
                        'jobName' => 'JOBNAME'
                    ]
                ),
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            ["Document id '200116', 'JOBNAME' queued for print"],
            $result->getMessages()
        );
    }
}
