<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\ValidEcmtPermits as QueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits as Query;
use Mockery as m;

/**
 * ValidEcmtPermits Test
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class ValidEcmtPermitsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('IrhpPermit', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = ['id' => 1, 'page' => 1, 'limit' => 10];
        $query = Query::create($data);

        $irhpPermit = m::mock(IrhpPermit::class)->makePartial();
        $irhpPermit->shouldReceive('serialize')
            ->andReturn([]);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchByEcmtApplicationPaginated')
            ->with($query)
            ->andReturn($irhpPermit)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $expected = [
            'results' => [],
            'count' => 'COUNT'
        ];

        $this->assertEquals($expected, $result);
    }
}
