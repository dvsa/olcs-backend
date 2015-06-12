<?php

/**
 * StayList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing\StayList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Hearing as StayRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Hearing\StayList as Qry;

/**
 * StayList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class StayListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StayList();
        $this->mockRepo('Stay', StayRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Stay']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Stay']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
