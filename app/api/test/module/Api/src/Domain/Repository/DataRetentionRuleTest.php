<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Transfer\Query\DataRetention\RuleList;
use Mockery as m;

/**
 * Class DataRetentionRuleTest
 */
class DataRetentionRuleTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(DataRetentionRule::class);
    }

    public function testFetchEnabledRules()
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('m.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->isNull')->with('m.deletedDate')->once()->andReturn('expr1');
        $qb->shouldReceive('andWhere')->with('expr1')->twice()->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULT']);

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $result = $this->sut->fetchEnabledRules();

        $this->assertSame(['RESULT'], $result);
    }

    public function testFetchEnabledRulesWithQueryBuilderAndIsReview()
    {
        $query = RuleList::create(
            ['sort' => 'id', 'order' => 'DESC']
        );

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('m.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('m.actionType', ':actionType')->once()->andReturn('expr1');
        $qb->shouldReceive('expr->isNull')->with('m.deletedDate')->once()->andReturn('expr1');
        $qb->shouldReceive('andWhere')->with('expr1')->times(3)->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once()->andReturn('expr1');
        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULT']);

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturnSelf();

        $result = $this->sut->fetchEnabledRules($query, true);

        $this->assertSame(['RESULT'], $result);
    }

    public function testRunProc()
    {
        $this->em->shouldReceive('getConnection->exec')->with('CALL proc(99)')->once()->andReturn(12);

        $result = $this->sut->runProc('proc', 99);

        $this->assertSame(12, $result);
    }

    public function testRunActionProc()
    {
        $this->em->shouldReceive('getConnection->fetchAll')->with('CALL proc(123, 99)')->once()->andReturn(['RESULTS']);

        $result = $this->sut->runActionProc('proc', 123, 99);

        $this->assertTrue($result);
    }

    public function testRunActionProcFalse()
    {
        $this->em->shouldReceive('getConnection->fetchAll')->with('CALL proc(123, 99)')->once()
            ->andReturn(['RESULTS' => ['This is an ERROR']]);

        $result = $this->sut->runActionProc('proc', 123, 99);

        $this->assertFalse($result);
    }
}
