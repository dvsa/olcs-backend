<?php

/**
 * BusRegSearchViewList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\BusRegSearchView;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView\BusRegSearchViewList;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * BusRegSearchViewList Test
 */
class BusRegSearchViewListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusRegSearchViewList();
        $this->mockRepo('BusRegSearchView', Repository\BusRegSearchView::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'licNo' => 'UB1234567',
                'status' => 'breg_s_cancellation',
                'organisationName' => 'ORG1'
            ]
        );

        $mockRecord = m::mock();
        $mockRecord->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $this->repoMap['BusRegSearchView']
            ->shouldReceive('fetchList')
            ->once()
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockRecord])
            ->shouldReceive('fetchCount')
            ->once()
            ->with($query)
            ->andReturn(1);

        $expected = [
            'result' => [
                ['foo' => 'bar']
            ],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
