<?php

/**
 * Team List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Team;

use Dvsa\Olcs\Api\Domain\QueryHandler\Team\TeamList as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Team\TeamList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;
use Mockery as m;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * Team List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TeamListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Team', TeamRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

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
