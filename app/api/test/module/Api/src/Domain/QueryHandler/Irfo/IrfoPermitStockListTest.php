<?php

/**
 * IrfoPermitStockList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoPermitStockList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPermitStock as IrfoPermitStockRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoPermitStockList as Qry;

/**
 * IrfoPermitStockList Test
 */
class IrfoPermitStockListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoPermitStockList();
        $this->mockRepo('IrfoPermitStock', IrfoPermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoPermitStock']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoPermitStock']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
