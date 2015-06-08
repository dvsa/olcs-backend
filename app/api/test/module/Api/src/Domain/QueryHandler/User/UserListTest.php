<?php

/**
 * UserListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Trailers;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Transfer\Query\User\UserList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * UserListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UserListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $this->repoMap['User']->shouldReceive('fetchList')->with($query)->andReturn('LIST');
        $this->repoMap['User']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['result' => 'LIST', 'count' => 'COUNT'], $result);
    }
}
