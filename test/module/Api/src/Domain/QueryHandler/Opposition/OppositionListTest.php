<?php

/**
 * OppositionList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Opposition;

use Dvsa\Olcs\Api\Domain\QueryHandler\Opposition\OppositionList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as OppositionRepo;
use Dvsa\Olcs\Transfer\Query\Opposition\OppositionList as Qry;
use Mockery as m;

/**
 * OppositionList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class OppositionListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OppositionList();
        $this->mockRepo('Opposition', OppositionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['Opposition']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['Opposition']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
