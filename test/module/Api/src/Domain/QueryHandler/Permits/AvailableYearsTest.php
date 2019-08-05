<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\AvailableYears;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableYears as AvailableYearsQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class AvailableYearsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableYears();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        $this->mockedSmServices = [
            'PermitsShortTermEcmtStockAvailabilityChecker' => m::mock(StockAvailabilityChecker::class)
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $ips1Id = 20;
        $ips2Id = 40;
        $ips3Id = 60;

        $ips1 = m::mock(IrhpPermitStock::class);
        $ips1->shouldReceive('getId')
            ->andReturn($ips1Id);
        $ips1->shouldReceive('getValidityYear')
            ->andReturn(3030);

        $ips2 = m::mock(IrhpPermitStock::class);
        $ips2->shouldReceive('getId')
            ->andReturn($ips2Id);
        $ips2->shouldReceive('getValidityYear')
            ->andReturn(3031);

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
            $ipw1,
            $ipw2,
            $ipw3,
        ];

        $query = AvailableYearsQuery::create(
            [
                'type' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByType')
            ->with(
                $query->getType(),
                m::type(DateTime::class)
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

        $this->assertEquals(
            [
                'years' => [3030, 3031]
            ],
            $this->sut->handleQuery($query)
        );
    }

    /**
     * @dataProvider dpTestHandleQueryUnsupportedType
     */
    public function testHandleQueryUnsupportedType($unsupportedTypeId)
    {
        $query = AvailableYearsQuery::create(
            [
                'type' => $unsupportedTypeId
            ]
        );

        $this->assertEquals(
            [
                'years' => []
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function dpTestHandleQueryUnsupportedType()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
        ];
    }

    public function testHandleQueryNoYears()
    {
        $query = AvailableYearsQuery::create(
            [
                'type' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByType')
            ->with(
                $query->getType(),
                m::type(DateTime::class)
            )
            ->andReturn([]);

        $this->assertEquals(
            [
                'years' => []
            ],
            $this->sut->handleQuery($query)
        );
    }
}
