<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
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
        $qb->shouldReceive('andWhere')->with('expr1')->once()->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULT']);

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $result = $this->sut->fetchEnabledRules();

        $this->assertSame(['RESULT'], $result);
    }

    public function testRunProc()
    {
        $this->em->shouldReceive('getConnection->exec')->with('CALL proc(99)')->once()->andReturn(12);

        $result = $this->sut->runProc('proc', 99);

        $this->assertSame(12, $result);
    }
}
