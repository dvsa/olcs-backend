<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\StockScoringPermitted;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class StockScoringPermittedTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StockScoringPermitted();
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery($lastOpenWindow, $statusId, $expectedPermitted)
    {
        $stockId = 47;

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getId')
            ->andReturn($stockId);

        $statusRefData = m::mock(RefData::class);
        $statusRefData->shouldReceive('getId')
            ->andReturn($statusId);

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getStatus')
            ->andReturn($statusRefData);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindowByStockId')
            ->andReturn($lastOpenWindow);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedPermitted, $result['result']);
    }

    public function scenariosProvider()
    {
        $lastOpenWindow = m::mock(IrhpPermitWindow::class);

        return [
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_NEVER_RUN, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_PENDING, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_IN_PROGRESS, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_SUCCESSFUL, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_ACCEPT_PENDING, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL, false],
            [$lastOpenWindow, IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
            [null, IrhpPermitStock::STATUS_SCORING_NEVER_RUN, true],
            [null, IrhpPermitStock::STATUS_SCORING_PENDING, false],
            [null, IrhpPermitStock::STATUS_SCORING_IN_PROGRESS, false],
            [null, IrhpPermitStock::STATUS_SCORING_SUCCESSFUL, true],
            [null, IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL, true],
            [null, IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL, true],
            [null, IrhpPermitStock::STATUS_ACCEPT_PENDING, false],
            [null, IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS, false],
            [null, IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL, false],
            [null, IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL, false],
            [null, IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
        ];
    }
}
