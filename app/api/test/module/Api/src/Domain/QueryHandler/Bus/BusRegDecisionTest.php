<?php

/**
 * BusReg Decision Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\BusRegDecision;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\BusRegDecision as Qry;

/**
 * BusReg Decision Test
 */
class BusRegDecisionTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusRegDecision();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $bus = m::mock(BusReg::class)->makePartial();
        $bus->shouldReceive('serialize')
            ->andReturn(['foo'])
            ->shouldReceive('getDecision')
            ->andReturn(['bar']);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($bus);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(['foo', 'decision' => ['bar']], $result->serialize());
    }
}
