<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\RegistrationHistoryList;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\ByRouteNo as RouteNoQuery;
use Dvsa\Olcs\Transfer\Query\Bus\RegistrationHistoryList as RegListQuery;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusEntity;
use Doctrine\ORM\Query;
use Mockery as m;

/**
 * RegistrationHistoryList Test
 */
class RegistrationHistoryListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(RegistrationHistoryList::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $busRegId = 99;
        $routeNo = 12345;
        $sort = 'sort';
        $order = 'DESC';
        $page = 1;
        $limit = 10;

        $regListQueryParams = [
            'id' => $busRegId,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'limit' => $limit
        ];

        $regListQuery = RegListQuery::create($regListQueryParams);

        $mockBusReg = m::mock(BusEntity::class);
        $mockBusReg->shouldReceive('getRouteNo')->andReturn($routeNo);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($regListQuery)
            ->andReturn($mockBusReg);

        $this->sut->shouldReceive('getQueryHandler->handleQuery')
            //->with($routeNoQuery)
                ->with(m::type(RouteNoQuery::class))
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($regListQuery));
    }
}
