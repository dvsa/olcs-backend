<?php

/**
 * Traffic Area list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TrafficArea;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\TrafficArea\TrafficAreaList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as Repo;
use Dvsa\Olcs\Transfer\Query\TrafficArea\TrafficAreaList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

/**
 * Traffic Area list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TrafficAreaListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TrafficArea', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $trafficArea = m::mock(TrafficAreaEntity::class);
        $trafficArea->shouldReceive('serialize')->once()->andReturn('SERIALIZED');

        $this->repoMap['TrafficArea']->shouldReceive('fetchList')
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)->andReturn([$trafficArea]);
        $this->repoMap['TrafficArea']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
