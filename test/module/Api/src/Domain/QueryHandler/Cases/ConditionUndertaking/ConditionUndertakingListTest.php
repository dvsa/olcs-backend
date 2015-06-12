<?php

/**
 * ConditionUndertakingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ConditionUndertaking\ConditionUndertakingList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\ConditionUndertaking\ConditionUndertakingList as Qry;

/**
 * ConditionUndertakingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ConditionUndertakingListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ConditionUndertakingList();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
