<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\AvailableStocks;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableStocks as AvailableStocksQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class AvailableStocksTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableStocks();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        $this->mockedSmServices = [
            'PermitsShortTermEcmtStockAvailabilityChecker' => m::mock(StockAvailabilityChecker::class)
        ];

        parent::setUp();
    }

    public function testHandleQueryEcmtShortTerm2020()
    {
        $irhpPermitType = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $year = 2020;

        $ips1Id = 20;
        $ips2Id = 40;
        $ips3Id = 60;

        $ips1 = m::mock(IrhpPermitStock::class);
        $ips1->shouldReceive('getId')
            ->andReturn($ips1Id);
        $ips1->shouldReceive('getPeriodNameKey')
            ->andReturn('period.name.key.1');

        $ips2 = m::mock(IrhpPermitStock::class);
        $ips2->shouldReceive('getId')
            ->andReturn($ips2Id);
        $ips2->shouldReceive('getPeriodNameKey')
            ->andReturn('period.name.key.2');

        $ips3 = m::mock(IrhpPermitStock::class);
        $ips3->shouldReceive('getId')
            ->andReturn($ips3Id);

        $ipw1 = m::mock(IrhpPermitWindow::class);
        $ipw1->shouldReceive('getIrhpPermitStock')
            ->andReturn($ips1);

        $ipw2 = m::mock(IrhpPermitWindow::class);
        $ipw2->shouldReceive('getIrhpPermitStock')
            ->andReturn($ips2);

        $ipw3 = m::mock(IrhpPermitWindow::class);
        $ipw3->shouldReceive('getIrhpPermitStock')
            ->andReturn($ips3);

        $irhpPermitWindows = [
            $ipw3,
            $ipw2,
            $ipw1,
        ];

        $query = AvailableStocksQuery::create(
            [
                'irhpPermitType' => $irhpPermitType,
                'year' => $year,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByTypeYear')
            ->with(
                $query->getIrhpPermitType(),
                m::type(DateTime::class),
                $query->getYear()
            )
            ->andReturn($irhpPermitWindows);

        $this->mockedSmServices['PermitsShortTermEcmtStockAvailabilityChecker']->shouldReceive('hasAvailability')
            ->with($ips1Id)
            ->andReturn(true);
        $this->mockedSmServices['PermitsShortTermEcmtStockAvailabilityChecker']->shouldReceive('hasAvailability')
            ->with($ips2Id)
            ->andReturn(true);
        $this->mockedSmServices['PermitsShortTermEcmtStockAvailabilityChecker']->shouldReceive('hasAvailability')
            ->with($ips3Id)
            ->andReturn(false);

        $this->assertSame(
            [
                'stocks' => [
                    $ips1Id => [
                        'id' => $ips1Id,
                        'periodNameKey' => 'period.name.key.1',
                    ],
                    $ips2Id => [
                        'id' => $ips2Id,
                        'periodNameKey' => 'period.name.key.2',
                    ],
                ],
            ],
            $this->sut->handleQuery($query)
        );
    }

    /**
     * @dataProvider dpTestHandleQueryUnsupportedType
     */
    public function testHandleQueryUnsupportedType($irhpPermitType, $year)
    {
        $query = AvailableStocksQuery::create(
            [
                'irhpPermitType' => $irhpPermitType,
                'year' => $year,
            ]
        );

        $this->assertEquals(
            [
                'stocks' => []
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function dpTestHandleQueryUnsupportedType()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT, 2019],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT, 2020],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, 2019],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, 2019],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, 2020],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, 2019],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, 2020],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, 2019],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, 2020],
        ];
    }

    public function testHandleQueryNoStocks()
    {
        $query = AvailableStocksQuery::create(
            [
                'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'year' => 2020,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByTypeYear')
            ->with(
                $query->getIrhpPermitType(),
                m::type(DateTime::class),
                $query->getYear()
            )
            ->andReturn([]);

        $this->assertEquals(
            [
                'stocks' => []
            ],
            $this->sut->handleQuery($query)
        );
    }
}
