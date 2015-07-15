<?php

/**
 * Reassign Tasks Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Task;

use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\Task\ReassignTasks as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Task\ReassignTasks;
use Dvsa\Olcs\Api\Domain\Repository\Task;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Reassign Tasks Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReassignTasksTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReassignTasks();
        $this->mockRepo('Task', Task::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            User::class => [
                1 => m::mock(User::class)
            ],
            Team::class => [
                2 => m::mock(Team::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'ids' => [123, 321],
                'user' => 1,
                'team' => 2
            ]
        );

        $task1 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task1->shouldReceive('setAssignedToUser')->once()->with($this->references[User::class][1]);
        $task1->shouldReceive('setAssignedToTeam')->once()->with($this->references[Team::class][2]);
        $task2 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task2->shouldReceive('setAssignedToUser')->once()->with($this->references[User::class][1]);
        $task2->shouldReceive('setAssignedToTeam')->once()->with($this->references[Team::class][2]);

        $this->repoMap['Task']->shouldReceive('fetchById')
            ->with(123)
            ->andReturn($task1)
            ->shouldReceive('fetchById')
            ->with(321)
            ->andReturn($task2)
            ->shouldReceive('save')
            ->with($task1)
            ->shouldReceive('save')
            ->with($task2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Task(s) reassigned'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandUnassignedUser()
    {
        $command = Cmd::create(
            [
                'ids' => [123, 321],
                'team' => 2
            ]
        );

        $task1 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task1->shouldReceive('setAssignedToUser')->once()->with(null);
        $task1->shouldReceive('setAssignedToTeam')->once()->with($this->references[Team::class][2]);
        $task2 = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task2->shouldReceive('setAssignedToUser')->once()->with(null);
        $task2->shouldReceive('setAssignedToTeam')->once()->with($this->references[Team::class][2]);

        $this->repoMap['Task']->shouldReceive('fetchById')
            ->with(123)
            ->andReturn($task1)
            ->shouldReceive('fetchById')
            ->with(321)
            ->andReturn($task2)
            ->shouldReceive('save')
            ->with($task1)
            ->shouldReceive('save')
            ->with($task2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Task(s) reassigned'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
