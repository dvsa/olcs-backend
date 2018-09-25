<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\StockAcceptPermitted;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class StockAcceptPermittedTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StockAcceptPermitted();
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery($statusId, $expectedPermitted)
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

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedPermitted, $result['result']);
    }

    public function scenariosProvider()
    {
        return [
            [IrhpPermitStock::STATUS_SCORING_NEVER_RUN, false],
            [IrhpPermitStock::STATUS_SCORING_PENDING, false],
            [IrhpPermitStock::STATUS_SCORING_IN_PROGRESS, false],
            [IrhpPermitStock::STATUS_SCORING_SUCCESSFUL, true],
            [IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL, false],
            [IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL, false],
            [IrhpPermitStock::STATUS_ACCEPT_PENDING, false],
            [IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS, false],
            [IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL, false],
            [IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL, false],
            [IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
        ];
    }
}
