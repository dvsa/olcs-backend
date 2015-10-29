<?php

/**
 * Create Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Task;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter;
use Dvsa\Olcs\Api\Domain\Repository\Task;
use Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule as TaskAllocationRuleEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Create Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateTaskTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateTask();
        $this->mockRepo('Task', Task::class);
        $this->mockRepo('TaskAllocationRule', TaskAllocationRule::class);
        $this->mockRepo('SystemParameter', SystemParameter::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TaskEntity::TYPE_SIMPLE
        ];

        $this->categoryReferences = [
            1 => m::mock(Category::class)
        ];

        $this->subCategoryReferences = [
            2 => m::mock(SubCategory::class)
        ];

        $this->references = [
            User::class => [
                11 => m::mock(User::class),
                888 => m::mock(User::class),
                999 => m::mock(User::class),
            ],
            Team::class => [
                22 => m::mock(Team::class),
                999 => m::mock(Team::class)
            ],
            Application::class => [
                111 => m::mock(Application::class)
            ],
            Licence::class => [
                222 => m::mock(Licence::class)
            ],
            BusReg::class => [
                64 => m::mock(BusReg::class)
            ],
            Cases::class => [
                164 => m::mock(Cases::class)
            ],
            TransportManager::class => [
                264 => m::mock(TransportManager::class)
            ],
            Organisation::class => [
                364 => m::mock(Organisation::class)
            ],
            Submission::class => [
                765 => m::mock(Cases::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'category' => 1,
            'subCategory' => 2,
            'assignedToUser' => 11,
            'assignedToTeam' => 22,
            'application' => 111,
            'licence' => 222,
            'actionDate' => '2015-01-01',
            'description' => 'Some task',
            'isClosed' => false,
            'urgent' => false,
            'busReg' => 64,
            'case' => 164,
            'transportManager' => 264,
            'irfoOrganisation' => 364,
            'submission' => 765,
            'assignedByUser' => 999,
        ];

        $command = Cmd::create($data);

        $this->repoMap['Task']->shouldReceive('save')
            ->once()
            ->with(m::type(TaskEntity::class))
            ->andReturnUsing(
                function (TaskEntity $task) {
                    $task->setId(123);

                    $this->assertSame($this->categoryReferences[1], $task->getCategory());
                    $this->assertSame($this->subCategoryReferences[2], $task->getSubCategory());
                    $this->assertSame($this->references[User::class][11], $task->getAssignedToUser());
                    $this->assertSame($this->references[Team::class][22], $task->getAssignedToTeam());
                    $this->assertSame($this->references[Application::class][111], $task->getApplication());
                    $this->assertSame($this->references[Licence::class][222], $task->getLicence());

                    $this->assertSame($this->references[BusReg::class][64], $task->getBusReg());
                    $this->assertSame($this->references[Cases::class][164], $task->getCase());
                    $this->assertSame($this->references[Submission::class][765], $task->getSubmission());
                    $this->assertSame($this->references[TransportManager::class][264], $task->getTransportManager());
                    $this->assertSame($this->references[Organisation::class][364], $task->getIrfoOrganisation());

                    $this->assertEquals('2015-01-01', $task->getActionDate()->format('Y-m-d'));
                    $this->assertEquals('Some task', $task->getDescription());
                    $this->assertEquals(false, $task->getIsClosed());
                    $this->assertEquals(false, $task->getUrgent());

                    $this->assertEquals(null, $task->getCreatedBy());
                    $this->assertEquals(null, $task->getLastModifiedBy());
                    $this->assertEquals(new DateTime('now'), $task->getLastModifiedOn());

                    $this->assertSame($this->references[User::class][999], $task->getAssignedByUser());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 123
            ],
            'messages' => [
                'Task created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider rulesProvider
     */
    public function testHandleCommandWithDefaultAutoAssignment($rules)
    {
        $data = [
            'category' => 1,
            'subCategory' => 2,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'application' => 111,
            'licence' => 222,
            'actionDate' => '2015-01-01',
            'description' => 'Some task',
            'isClosed' => false,
            'urgent' => false
        ];
        $command = Cmd::create($data);

        $this->categoryReferences[1]->setTaskAllocationType($this->refData[TaskEntity::TYPE_SIMPLE]);
        $this->repoMap['TaskAllocationRule']->shouldReceive('fetchForSimpleTaskAssignment')
            ->with($this->categoryReferences[1])
            ->once()
            ->andReturn($rules);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with('task.default_team')
            ->once()
            ->andReturn(999)
            ->shouldReceive('fetchValue')
            ->with('task.default_user')
            ->once()
            ->andReturn(888);

        $this->repoMap['Task']->shouldReceive('save')
            ->once()
            ->with(m::type(TaskEntity::class))
            ->andReturnUsing(
                function (TaskEntity $task) {
                    $task->setId(123);
                    $this->assertSame($this->references[User::class][888], $task->getAssignedToUser());
                    $this->assertSame($this->references[Team::class][999], $task->getAssignedToTeam());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 123
            ],
            'messages' => [
                'Task created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithAutoAssignment()
    {
        $data = [
            'category' => 1,
            'subCategory' => 2,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'application' => 111,
            'licence' => 222,
            'actionDate' => '2015-01-01',
            'description' => 'Some task',
            'isClosed' => false,
            'urgent' => false
        ];
        $command = Cmd::create($data);

        /** @var TaskAllocationRuleEntity $rule */
        $rule = m::mock(TaskAllocationRuleEntity::class)->makePartial();
        $rule->setUser($this->references[User::class][888]);
        $rule->setTeam($this->references[Team::class][999]);

        $rules = [
            $rule
        ];
        $this->categoryReferences[1]->setTaskAllocationType($this->refData[TaskEntity::TYPE_SIMPLE]);
        $this->repoMap['TaskAllocationRule']->shouldReceive('fetchForSimpleTaskAssignment')
            ->with($this->categoryReferences[1])
            ->once()
            ->andReturn($rules);

        $this->repoMap['Task']->shouldReceive('save')
            ->once()
            ->with(m::type(TaskEntity::class))
            ->andReturnUsing(
                function (TaskEntity $task) {
                    $task->setId(123);
                    $this->assertSame($this->references[User::class][888], $task->getAssignedToUser());
                    $this->assertSame($this->references[Team::class][999], $task->getAssignedToTeam());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 123
            ],
            'messages' => [
                'Task created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function rulesProvider()
    {
        return [
            [
                []
            ],
            [
                [
                    'foo',
                    'bar'
                ]
            ]
        ];
    }
}
