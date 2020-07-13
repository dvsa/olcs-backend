<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as BusRegSearchView;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as SearchViewListQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\SearchViewList as SearchViewListQueryHandler;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as BusRegSearchViewEntity;
use Doctrine\ORM\Query;
use Mockery as m;
use Doctrine\ORM\Query as DoctrineQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;

/**
 * SearchViewList Test
 */
class SearchViewListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = m::mock(SearchViewListQueryHandler::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockRepo('BusRegSearchView', BusRegSearchView::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licId  = 99;
        $status = 'some_status'; // @Todo update in the view
        $sort   = 'sort';
        $order  = 'DESC';
        $page   = 1;
        $limit  = 10;

        $queryParams = [
            'licId'  => $licId,
            'status' => $status,
            'sort'   => $sort,
            'order'  => $order,
            'page'   => $page,
            'limit'  => $limit
        ];

        $result = new ArrayCollection([
            m::mock(BusRegSearchViewEntity::class)->makePartial(),
            m::mock(BusRegSearchViewEntity::class)->makePartial()
        ]);

        $listQuery = SearchViewListQuery::create($queryParams);

        $this->repoMap['BusRegSearchView']->shouldReceive('fetchList')
            ->with($listQuery, DoctrineQuery::HYDRATE_OBJECT)
            ->andReturn($result);

        $this->repoMap['BusRegSearchView']->shouldReceive('fetchCount')
            ->with($listQuery)
            ->andReturn(count($result));

        $this->assertEquals(
            ['result' => (new ResultList($result, []))->serialize(), 'count' => count($result)],
            $this->sut->handleQuery($listQuery)
        );
    }
}
