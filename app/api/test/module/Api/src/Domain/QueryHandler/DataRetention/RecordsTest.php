<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\Repository\DataRetention as DataRetentionRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention\Records as QueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records as Query;
use Doctrine\ORM\Query as DoctrineQuery;
use Mockery as m;

/**
 * Records Test
 */
class RecordsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('DataRetention', DataRetentionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['dataRetentionRuleId' => 1]);

        $mockRuleList = m::mock();
        $mockRuleList->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['DataRetention']
            ->shouldReceive('fetchAllWithEnabledRules')
            ->with(
                $query
            )
            ->once()
            ->andReturn([$mockRuleList])
            ->getMock();

        $this->assertSame(
            [
                'result'    => ['foo'],
                'count'     => 1,
            ],
            $this->sut->handleQuery($query)
        );
    }
}
