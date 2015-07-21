<?php

/**
 * MarkersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Markers as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Licence\Markers as Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * MarkersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class MarkersTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 716]);

        $mockLicence = m::mock(Licence::class)->makePartial()
            ->shouldReceive('serialize')->with([])->once()->andReturn(['LICENCE'])
            ->shouldReceive('getId')->with()->once()->andReturn(716)
            ->getMock();
        $mockContinuationDetail = m::mock(\Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail::class)
            ->shouldReceive('serialize')->with(['continuation', 'licence'])->once()->andReturn(['CD'])
            ->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockLicence);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(716)
            ->andReturn([$mockContinuationDetail]);

        $response = $this->sut->handleQuery($query);
        $this->assertSame(['LICENCE', 'continuationMarker' => ['CD']], $response->serialize());
    }

    public function testHandleQueryNoContinuationDetail()
    {
        $query = Query::create(['id' => 716]);

        $mockLicence = m::mock(Licence::class)->makePartial()
            ->shouldReceive('serialize')->with([])->once()->andReturn(['LICENCE'])
            ->shouldReceive('getId')->with()->once()->andReturn(716)
            ->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockLicence);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(716)
            ->andReturn([]);

        $response = $this->sut->handleQuery($query);
        $this->assertSame(['LICENCE', 'continuationMarker' => null], $response->serialize());
    }
}
