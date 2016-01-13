<?php

/**
 * HearingList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Pi;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi\HearingList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as PiHearingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Impounding\ImpoundingList as Qry;
use Mockery as m;

/**
 * HearingList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new HearingList();
        $this->mockRepo('PiHearing', PiHearingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['PiHearing']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['PiHearing']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn($count);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], ['foo']);
    }
}
