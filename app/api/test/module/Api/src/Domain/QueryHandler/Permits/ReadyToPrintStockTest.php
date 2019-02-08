<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\ReadyToPrintStock;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrintStock as ReadyToPrintStockQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class ReadyToPrintStockTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReadyToPrintStock();
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $irhpPermitType = 1;
        $country = 'DE';

        $irhpPermitStocks = [
            m::mock(IrhpPermitStock::class),
            m::mock(IrhpPermitStock::class),
            m::mock(IrhpPermitStock::class)
        ];

        $query = ReadyToPrintStockQuery::create(
            [
                'irhpPermitType' => $irhpPermitType,
                'country' => $country,
            ]
        );

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchReadyToPrint')
            ->with($irhpPermitType, $country)
            ->andReturn($irhpPermitStocks);

        $this->assertEquals(
            ['results' => $irhpPermitStocks],
            $this->sut->handleQuery($query)
        );
    }
}
