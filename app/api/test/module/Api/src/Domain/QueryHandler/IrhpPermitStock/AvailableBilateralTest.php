<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermitStock;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitStock\AvailableBilateral;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Transfer\Query\IrhpPermitStock\AvailableBilateral as AvailableBilateralQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class AvailableBilateralTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableBilateral();
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = AvailableBilateralQuery::create(['country' => 'NO']);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchOpenBilateralStocksByCountry')
            ->with($query->getCountry(), m::type(DateTime::class))
            ->andReturn(['stocks']);

        $this->assertEquals(
            ['stocks'],
            $this->sut->handleQuery($query)
        );
    }
}
