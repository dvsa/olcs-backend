<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\ByRouteNo;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\ByRouteNo as Qry;
use Doctrine\ORM\Query;
use Mockery as m;

/**
 * ByRouteNo Test
 */
class ByRouteNoTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ByRouteNo();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $mockResult = m::mock(\ArrayIterator::class);
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['Bus']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockResult]);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query, Query::HYDRATE_OBJECT));
    }
}
