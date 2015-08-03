<?php

/**
 * Update Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Task;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\Task\UpdateTask as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Task\UpdateTask;
use Dvsa\Olcs\Api\Domain\Repository\Task;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTaskTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTask();
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

        $this->categoryReferences = [
            11 => m::mock(Category::class)
        ];

        $this->subCategoryReferences = [
            22 => m::mock(SubCategory::class)
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 123,
            'version' => 1,
            'description' => 'foo bar',
            'actionDate' => '2015-01-01',
            'urgent' => 'Y',
            'category' => 11,
            'subCategory' => 22,
            'assignedToUser' => 1,
            'assignedToTeam' => 2
        ];

        $command = Cmd::create($data);

        /** @var \Dvsa\Olcs\Api\Entity\Task\Task $task */
        $task = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class)->makePartial();

        $this->repoMap['Task']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($task)
            ->shouldReceive('save')
            ->with($task);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Task updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('foo bar', $task->getDescription());
        $this->assertEquals('2015-01-01', $task->getActionDate()->format('Y-m-d'));
        $this->assertEquals('Y', $task->getUrgent());
        $this->assertSame($this->categoryReferences[11], $task->getCategory());
        $this->assertSame($this->subCategoryReferences[22], $task->getSubCategory());
        $this->assertSame($this->references[User::class][1], $task->getAssignedToUser());
        $this->assertSame($this->references[Team::class][2], $task->getAssignedToTeam());
    }

    public function testHandleCommandUnassignedUser()
    {
        $data = [
            'id' => 123,
            'version' => 1,
            'description' => 'foo bar',
            'actionDate' => '2015-01-01',
            'urgent' => 'Y',
            'category' => 11,
            'subCategory' => 22,
            'assignedToTeam' => 2
        ];

        $command = Cmd::create($data);

        /** @var \Dvsa\Olcs\Api\Entity\Task\Task $task */
        $task = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class)->makePartial();

        $this->repoMap['Task']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($task)
            ->shouldReceive('save')
            ->with($task);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Task updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('foo bar', $task->getDescription());
        $this->assertEquals('2015-01-01', $task->getActionDate()->format('Y-m-d'));
        $this->assertEquals('Y', $task->getUrgent());
        $this->assertSame($this->categoryReferences[11], $task->getCategory());
        $this->assertSame($this->subCategoryReferences[22], $task->getSubCategory());
        $this->assertNull($task->getAssignedToUser());
        $this->assertSame($this->references[Team::class][2], $task->getAssignedToTeam());
    }
}
