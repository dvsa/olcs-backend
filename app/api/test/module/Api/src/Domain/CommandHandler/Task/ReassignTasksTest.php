<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Task;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\Task\ReassignTasks as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Task\ReassignTasks;
use Dvsa\Olcs\Api\Domain\Repository\Task;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Task\ReassignTasks
 */
class ReassignTasksTest extends CommandHandlerTestCase
{
    const TASK_ID_1 = 9990;
    const TASK_ID_2 = 9991;

    const TEAM_ID_1 = 7001;
    const TEAM_ID_2 = 7002;

    const USER_ID_1 = 6001;
    const USER_ID_2 = 6002;

    /** @var ReassignTasks */
    protected $sut;
    /** @var  m\MockInterface | Team */
    private $mockTeam;
    /** @var  m\MockInterface | Team */
    private $mockTeam2;
    /** @var  m\MockInterface | User */
    private $mockUser;
    /** @var  m\MockInterface | User */
    private $mockUser2;

    public function setUp()
    {
        $this->sut = new ReassignTasks();

        $this->mockRepo('Task', Task::class);

        $this->mockTeam = m::mock(Team::class);
        $this->mockTeam2 = m::mock(Team::class);

        $this->mockUser = m::mock(User::class);
        $this->mockUser->shouldReceive('getTeam')->andReturn($this->mockTeam2);

        $this->mockUser2 = m::mock(User::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            User::class => [
                self::USER_ID_1 => $this->mockUser,
                self::USER_ID_2 => $this->mockUser2,
            ],
            Team::class => [
                self::TEAM_ID_1 => $this->mockTeam,
                self::TEAM_ID_2 => $this->mockTeam2,
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'ids' => [self::TASK_ID_1, self::TASK_ID_2],
                'user' => self::USER_ID_1,
                'team' => self::TEAM_ID_1,
            ]
        );

        $task1 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task1->shouldReceive('setAssignedToUser')->once()->with($this->mockUser);
        $task1->shouldReceive('setAssignedToTeam')->once()->with($this->mockTeam);

        $task2 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task2->shouldReceive('setAssignedToUser')->once()->with($this->mockUser);
        $task2->shouldReceive('setAssignedToTeam')->once()->with($this->mockTeam);

        $this->repoMap['Task']
            ->shouldReceive('fetchById')->with(self::TASK_ID_1)->andReturn($task1)
            ->shouldReceive('fetchById')->with(self::TASK_ID_2)->andReturn($task2)
            ->shouldReceive('save')->with($task1)
            ->shouldReceive('save')->with($task2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Task(s) reassigned',
            ],
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandUnassignedUser()
    {
        $command = Cmd::create(
            [
                'ids' => [
                    self::TASK_ID_1,
                    self::TASK_ID_2,
                ],
                'team' => self::TEAM_ID_1,
            ]
        );

        $task1 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task1->shouldReceive('setAssignedToUser')->once()->with(null);
        $task1->shouldReceive('setAssignedToTeam')->once()->with($this->mockTeam);
        $task2 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task2->shouldReceive('setAssignedToUser')->once()->with(null);
        $task2->shouldReceive('setAssignedToTeam')->once()->with($this->mockTeam);

        $this->repoMap['Task']
            ->shouldReceive('fetchById')->with(self::TASK_ID_1)->andReturn($task1)
            ->shouldReceive('fetchById')->with(self::TASK_ID_2)->andReturn($task2)
            ->shouldReceive('save')->with($task1)
            ->shouldReceive('save')->with($task2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Task(s) reassigned',
            ],
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandTeamFromUser()
    {
        $command = Cmd::create(
            [
                'ids' => [self::TASK_ID_1],
                'user' => self::USER_ID_1,
            ]
        );

        $task1 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task1->shouldReceive('setAssignedToUser')->once()->with($this->mockUser);
        $task1->shouldReceive('setAssignedToTeam')->once()->with($this->mockTeam2);

        $this->repoMap['Task']
            ->shouldReceive('fetchById')->with(self::TASK_ID_1)->andReturn($task1)
            ->shouldReceive('save')->with($task1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Task(s) reassigned',
            ],
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandFailTeamInvalid()
    {
        $this->setExpectedException(ValidationException::class, ReassignTasks::ERR_TEAM_INVALID);

        $this->sut->handleCommand(
            Cmd::create(
                [
                    'ids' => [self::TASK_ID_1],
                ]
            )
        );
    }
}
