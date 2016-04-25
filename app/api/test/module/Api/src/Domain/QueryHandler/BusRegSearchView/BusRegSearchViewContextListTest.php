<?php

/**
 * BusRegSearchViewContextList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\BusRegSearchView;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView\BusRegSearchViewContextList;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewContextList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * BusRegSearchViewContextList Test
 */
class BusRegSearchViewContextListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusRegSearchViewContextList();
        $this->mockRepo('BusRegSearchView', Repository\BusRegSearchView::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'context' => 'foo'
            ]
        );

        $mockRecord = ['foo' => 'bar'];

        $this->repoMap['BusRegSearchView']
            ->shouldReceive('fetchDistinctList')
            ->once()
            ->with($query)
            ->andReturn([$mockRecord]);

        $expected = [
            'result' => [
                0 => 'bar'
            ],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
