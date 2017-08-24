<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records;
use Mockery as m;

/**
 * Class DataRetentionTest
 */
class DataRetentionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(DataRetention::class);
    }

    public function testFetchEntitiesToDelete()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('dataRetentionRule', 'drr')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['FOO']);

        $result = $this->sut->fetchEntitiesToDelete(12);

        $this->assertSame(['FOO'], $result);

        $expectedQuery = '[QUERY] AND drr.isEnabled = 1 AND m.toAction = 1 AND m.actionConfirmation = 1 AND '.
            'm.actionedDate IS NULL AND m.nextReviewDate IS NULL LIMIT 12';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchAllWithEnabledRules()
    {
        $query = Records::create(
            ['dataRetentionRuleId' => 13, 'sort' => 'id', 'order' => 'DESC']
        );

        /** @var QueryBuilder|m\mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('andWhere')->times(3)->andReturnSelf();
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', 'Review')->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', 13)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->isNull')->with('m.deletedDate')->once()->andReturn('expr1');
        $qb->shouldReceive('setParameter')->with(':actionType', 'Review')->once()->andReturn('expr1');
        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULT']);

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('with')
            ->with('dataRetentionRule', 'drr')
            ->andReturnSelf()
            ->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturnSelf();

        $result = $this->sut->fetchAllWithEnabledRules($query);

        $this->assertSame(['RESULT'], $result);
    }
}
