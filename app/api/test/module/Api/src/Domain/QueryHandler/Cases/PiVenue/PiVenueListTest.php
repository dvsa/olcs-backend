<?php

/**
 * PiVenue List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\PiVenue;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\PiVenue\PiVenueList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiVenue as PiVenueRepo;
use Dvsa\Olcs\Transfer\Query\Cases\PiVenue\PiVenueList as Qry;
use Mockery as m;

/**
 * PiVenue List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PiVenueListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PiVenueList();
        $this->mockRepo('PiVenue', PiVenueRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['PiVenue']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['PiVenue']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn($count);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], ['foo']);
    }
}
