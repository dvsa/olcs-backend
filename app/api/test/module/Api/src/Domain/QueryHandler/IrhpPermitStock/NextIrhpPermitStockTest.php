<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitStock\NextIrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class NextIrhpPermitStockTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new NextIrhpPermitStock();
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $feeType1 = m::mock(FeeType::class);
        $feeType2 = m::mock(FeeType::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('getNextIrhpPermitStockByPermitType')
            ->with('permit_ecmt', date("Y-m-d"))
            ->andReturn(null);

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getPermitType')
            ->andReturn('permit_ecmt');

        $result = $this->sut->handleQuery($query);
    }
}
