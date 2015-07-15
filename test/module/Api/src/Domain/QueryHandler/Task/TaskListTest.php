<?php

/**
 * Task List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Task;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Task\TaskList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TaskSearchView as Repo;
use Dvsa\Olcs\Transfer\Query\Task\TaskList as Qry;

/**
 * Task List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TaskList();
        $this->mockRepo('TaskSearchView', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['TaskSearchView']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(10);

        $this->assertEquals(['result' => ['foo' => 'bar'], 'count' => 10], $this->sut->handleQuery($query));
    }
}
