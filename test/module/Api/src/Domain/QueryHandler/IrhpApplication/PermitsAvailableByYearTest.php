<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\PermitsAvailableByYear;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\PermitsAvailableByYear as PermitsAvailableByYearQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use RuntimeException;
use Mockery as m;

class PermitsAvailableByYearTest extends QueryHandlerTestCase
{
    private $year;

    private $query;

    public function setUp()
    {
        $this->sut = new PermitsAvailableByYear();

        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        $this->mockedSmServices = [
            'PermitsShortTermEcmtStockAvailabilityChecker' => m::mock(StockAvailabilityChecker::class),
        ];

        $this->year = 2018;

        $this->query = PermitsAvailableByYearQry::create(['year' => $this->year]);

        parent::setUp();
    }

    /**
     * @dataProvider dpTestReturnStockAvailabilityWhenOneWindowOpen
     */
    public function testReturnStockAvailabilityWhenOneWindowOpen($hasAvailability)
    {
        $irhpPermitStockId = 28;

        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $irhpPermitWindow->shouldReceive('getIrhpPermitStock->getId')
            ->andReturn($irhpPermitStockId);

        $irhpPermitWindows = [$irhpPermitWindow];

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByTypeYear')
            ->with(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, m::type(DateTime::class), $this->year)
            ->andReturn($irhpPermitWindows);

        $this->mockedSmServices['PermitsShortTermEcmtStockAvailabilityChecker']->shouldReceive('hasAvailability')
            ->with($irhpPermitStockId)
            ->andReturn($hasAvailability);

        $this->assertEquals(
            ['permitsAvailable' => $hasAvailability],
            $this->sut->handleQuery($this->query)
        );
    }

    public function dpTestReturnStockAvailabilityWhenOneWindowOpen()
    {
        return [
            [true],
            [false],
        ];
    }

    public function testReturnFalseWhenNoWindowsOpen()
    {
        $irhpPermitStockId = 28;

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByTypeYear')
            ->with(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, m::type(DateTime::class), $this->year)
            ->andReturn([]);

        $this->mockedSmServices['PermitsShortTermEcmtStockAvailabilityChecker']->shouldReceive('hasAvailability')
            ->with($irhpPermitStockId)
            ->andReturn(false);

        $this->assertEquals(
            ['permitsAvailable' => false],
            $this->sut->handleQuery($this->query)
        );
    }

    public function testExceptionWhenTwoWindowsOpen()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpectedly found multiple windows open for year 2018');

        $irhpPermitWindows = [
            m::mock(IrhpPermitWindow::class),
            m::mock(IrhpPermitWindow::class),
        ];

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByTypeYear')
            ->with(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, m::type(DateTime::class), $this->year)
            ->andReturn($irhpPermitWindows);

        $this->sut->handleQuery($this->query);
    }
}
