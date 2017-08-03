<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule as DataRetentionRuleRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention\RuleList as QueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\DataRetention\RuleList as Query;
use Doctrine\ORM\Query as DoctrineQuery;
use Mockery as m;

/**
 * Rule List Test
 */
class RuleListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('DataRetentionRule', DataRetentionRuleRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

        $mockRuleList = m::mock();
        $mockRuleList->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['DataRetentionRule']
            ->shouldReceive('fetchEnabledRules')
            ->with()
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
