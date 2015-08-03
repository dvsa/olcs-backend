<?php

/**
 * Bus Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Bus;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\BusReg as Qry;

/**
 * Bus Test
 */
class BusTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Bus();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $bus = m::mock(BusReg::class)->makePartial();
        $bus->shouldReceive('serialize')
            ->andReturn(['foo']);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($bus);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(['foo'], $result->serialize());
    }
}
