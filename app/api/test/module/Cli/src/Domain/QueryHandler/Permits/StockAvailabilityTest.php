<?php

namespace Dvsa\OlcsTest\Cli\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Cli\Domain\QueryHandler\Permits\StockAvailability;
use Dvsa\Olcs\Cli\Domain\Query\Permits\StockAvailability as StockAvailabilityQuery;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Stock Availability test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockAvailabilityTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StockAvailability();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);
        $this->mockRepo('IrhpPermitRange', IrhpPermitRangeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $stockId = 6;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(200);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn(100);

        $query = m::mock(StockAvailabilityQuery::class);
        $query->shouldReceive('getStockId')
            ->andReturn($stockId);

        $this->assertEquals(
            ['result' => true, 'message' => null],
            $this->sut->handleQuery($query)
        );
    }

    public function testNullCombinedRangeSize()
    {
        $stockId = 6;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(null);

        $query = m::mock(StockAvailabilityQuery::class);
        $query->shouldReceive('getStockId')
            ->andReturn($stockId);

        $this->assertEquals(
            [
                'result' => false,
                'message' => 'No ranges available for this stock'
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function testNoPermitsAvailable()
    {
        $stockId = 6;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(100);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn(150);

        $query = m::mock(StockAvailabilityQuery::class);
        $query->shouldReceive('getStockId')
            ->andReturn($stockId);

        $this->assertEquals(
            [
                'result' => false,
                'message' => 'No permits available within the ranges of this stock'
            ],
            $this->sut->handleQuery($query)
        );
    }
}
