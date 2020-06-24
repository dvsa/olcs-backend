<?php

/**
 * Team List Data Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Team;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Team\TeamListData as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Team\TeamListData as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;
use Mockery as m;

/**
 * Team List Data Test
 */
class TeamListDataTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Team', TeamRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create([]);

        $mockTeam = m::mock();
        $mockTeam->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['Team']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn([$mockTeam])
            ->shouldReceive('fetchCount')
            ->with($query)
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->assertSame(
            [
                'result'    => ['foo'],
                'count'     => 1,
            ],
            $this->sut->handleQuery($query)
        );
    }
}
