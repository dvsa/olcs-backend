<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Scan;

use Dvsa\Olcs\Api\Domain\QueryHandler\Scan\GetSingle as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Scan\GetSingle as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * GetSingleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetSingleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Scan', \Dvsa\Olcs\Api\Domain\Repository\Scan::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1066]);

        $mockScan = m::mock(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class);
        $mockScan->shouldReceive('serialize')->once()->andReturn(['foo' => 'bar']);

        $this->repoMap['Scan']->shouldReceive('fetchUsingId')->with($query)->once()
            ->andReturn($mockScan);

        $result = $this->sut->handleQuery($query)->serialize();

        $this->assertEquals(['foo' => 'bar'], $result);
    }
}
