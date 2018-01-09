<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records as RecordsQry;
use Mockery as m;

/**
 * Class DataRetentionTest
 */
class DataRetentionTest extends RepositoryTestCase
{
    /** @var DataRetention */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(DataRetention::class, true);
    }

    public function testApplyListJoins()
    {
        $this->setUpSut(DataRetention::class, true);

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);

        $mockQb->shouldReceive('with')->once()->with('dataRetentionRule', 'drr')->andReturnSelf();
        $mockQb->shouldReceive('with')->once()->with('assignedTo', 'u')->andReturnSelf();
        $mockQb->shouldReceive('with')->once()->with('u.contactDetails', 'cd')->andReturnSelf();
        $mockQb->shouldReceive('with')->once()->with('cd.person', 'p')->andReturnSelf();
        $this->sut->shouldReceive('getQueryBuilder')->with()->once()->andReturn($mockQb);

        $this->sut->applyListJoins($mockQb);
    }

    public function testApplyListFiltersRecordsQry()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13, 'sort' => 'id', 'order' => 'DESC']
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryMarkedForDeletionY()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'markedForDeletion' => 'Y'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('m.actionConfirmation', ':actionConfirmation')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('actionConfirmation', 1)->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryMarkedForDeletionN()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'markedForDeletion' => 'N'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('m.actionConfirmation', ':actionConfirmation')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('actionConfirmation', 0)->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryWithNextReviewDeferred()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'nextReview' => 'deferred'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->isNotNull')->with('m.nextReviewDate')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryWithNextReviewPending()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'nextReview' => 'pending'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->isNull')->with('m.nextReviewDate')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryWithAssignedToUser()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'assignedToUser' => 1
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('m.assignedTo', ':assignedToUser')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('assignedToUser', 1)->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryWithAssignedToUserUnassigned()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'assignedToUser' => 'unassigned'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->isNull')->with('m.assignedTo')->once()->andReturn('expr0');
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr0')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersRecordsQryWithAssignedToUserAll()
    {
        $query = RecordsQry::create(
            ['dataRetentionRuleId' => 13,
                'sort' => 'id',
                'order' => 'DESC',
                'assignedToUser' => 'all'
            ]
        );

        /** @var QueryBuilder|m::mock $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('expr->eq')->with('drr.isEnabled', 1)->once()->andReturn('expr1');
        $qb->shouldReceive('expr->eq')->with('drr.actionType', ':actionType')->once()->andReturn('expr2');
        $qb->shouldReceive('expr->eq')->with('m.dataRetentionRule', ':dataRetentionRuleId')->once()->andReturn('expr3');
        $qb->shouldReceive('andWhere')->once()->with('expr1')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr2')->andReturnSelf();
        $qb->shouldReceive('andWhere')->once()->with('expr3')->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('actionType', 'Review')->once();
        $qb->shouldReceive('setParameter')->with('dataRetentionRuleId', 13)->once();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testRunCleanupProc()
    {
        $mockStatement = m::mock();
        $mockStatement
            ->shouldReceive('execute')
            ->once()
            ->with()
            ->andReturn(true)
            ->shouldReceive('closeCursor')
            ->andReturn(true);
        $this->em->shouldReceive('getConnection->prepare')->with('CALL sp_dr_cleanup(99, 123, 0)')->once()
            ->andReturn($mockStatement);

        $result = $this->sut->runCleanupProc(123, 99);

        $this->assertTrue($result);
    }

    public function testFetchAllProcessedForRule()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('getResult')
                ->with(Query::HYDRATE_ARRAY)->once()
                ->andReturn('RESULT')
                ->getMock()
        );

        $this->em->shouldReceive('getFilters->isEnabled')->with('soft-deleteable')->once()->andReturn([]);
        $this->em->shouldReceive('getFilters->enable')->with('soft-deleteable')->once()->andReturn([]);

        $this->assertEquals(
            'RESULT',
            $this->sut->fetchAllProcessedForRule(12, new \DateTime('2012-02-20'), new \DateTime('2017-12-10'))
        );

        $expectedQuery = 'BLAH '
            . 'AND m.dataRetentionRule = [[12]] '
            . 'AND m.deletedDate >= [[2012-02-20 00:00:00]] '
            . 'AND m.deletedDate < [[2017-12-11 00:00:00]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
