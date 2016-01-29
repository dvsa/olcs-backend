<?php

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Task;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Task\Task;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepo;
use Dvsa\Olcs\Transfer\Query\Task\Task as Qry;

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Task();
        $this->mockRepo('Task', TaskRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $task = m::mock(\Dvsa\Olcs\Api\Entity\Task\Task::class);
        $task->shouldReceive('serialize')
            ->with(
                [
                    'category',
                    'subCategory',
                    'assignedToTeam',
                    'assignedToUser',
                    'assignedByUser' => [
                        'contactDetails' => [
                            'person'
                        ]
                    ]
                ]
            )
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Task']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($task);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(['foo' => 'bar'], $result->serialize());
    }
}
