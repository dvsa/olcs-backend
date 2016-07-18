<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewList;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * BusRegSearchViewTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BusRegSearchViewTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByRegNo()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.regNo', ':regNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('regNo', 'REG0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULTS']);

        $this->assertSame('RESULTS', $this->sut->fetchByRegNo('REG0001'));
    }

    public function testFetchByRegNoNotFound()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.regNo', ':regNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('regNo', 'REG0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn([]);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchByRegNo('REG0001');
    }

    public function testFetchActiveByLicence()
    {
        $activeStatuses = [
            BusReg::STATUS_NEW,
            BusReg::STATUS_VAR,
            BusReg::STATUS_REGISTERED,
            BusReg::STATUS_CANCEL,
        ];

        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.licId', ':licence')->once()->andReturn('L_EXPR');
        $qb->shouldReceive('where')->with('L_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', '611')->once()->andReturnSelf();

        $qb->shouldReceive('expr->in')->with('m.busRegStatus', ':activeStatuses')->once()->andReturn('S_EXPR');
        $qb->shouldReceive('andWhere')->with('S_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('activeStatuses', $activeStatuses)->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULTS']);

        $this->assertSame(['RESULTS'], $this->sut->fetchActiveByLicence(611));
    }

    /**
     * @dataProvider provideContextGroupBys
     * @param $context
     */
    public function testFetchDistinctList($context, $expected)
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $qb->shouldReceive('distinct')->andReturnSelf();
        $qb->shouldReceive('select')->with($expected)->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULTS']);

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getContext')->andReturn($context);

        $this->assertSame(['RESULTS'], $this->sut->fetchDistinctList($mockQuery));
    }

    /**
     * @dataProvider provideContextGroupBys
     * @param $context
     */
    public function testFetchDistinctListWithOrganisationId($context, $expected)
    {
        $organisationId = 1;

        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $qb->shouldReceive('distinct')->andReturnSelf();
        $qb->shouldReceive('select')->with($expected)->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULTS']);

        $qb->shouldReceive('expr->eq')->with('m.organisationId', ':organisationId')->once()->andReturn('S_EXPR');
        $qb->shouldReceive('andWhere')->with('S_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('organisationId', $organisationId)->once()->andReturnSelf();

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getContext')->andReturn($context);

        $this->assertSame(['RESULTS'], $this->sut->fetchDistinctList($mockQuery, $organisationId));
    }

    /**
     * @dataProvider provideContextGroupBys
     * @param $context
     */
    public function testFetchDistinctListWithLocalAuthorityId($context, $expected)
    {
        $localAuthorityId = 1;

        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $qb->shouldReceive('distinct')->andReturnSelf();
        $qb->shouldReceive('select')->with($expected)->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULTS']);

        $qb->shouldReceive('expr->eq')->with('m.localAuthorityId', ':localAuthorityId')->once()->andReturn('S_EXPR');
        $qb->shouldReceive('andWhere')->with('S_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('localAuthorityId', $localAuthorityId)->once()->andReturnSelf();

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getContext')->andReturn($context);

        $this->assertSame(['RESULTS'], $this->sut->fetchDistinctList($mockQuery, null, $localAuthorityId));
    }

    /**
     * Data provider maps the relevent group by clauses that should be applied to the query given a certain context
     *
     * @return array
     */
    public function provideContextGroupBys()
    {
        return [
            [
                'licence', ['m.licId', 'm.licNo'],
            ],
            [
                'organisation', ['m.organisationId', 'm.organisationName']
            ],
            [
                'busRegStatus', ['m.busRegStatus', 'm.busRegStatusDesc']
            ],
        ];
    }

    public function testApplyListFiltersOperator()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('licId', '1234')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('busRegStatus', 'foo')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('organisationId', 342)
            ->andReturnSelf();

        $mockQ = BusRegSearchViewList::create(
            [
                'licId' => '1234',
                'busRegStatus' => 'foo',
                'organisationId' => 342
            ]
        );

        $this->sut->applyListFilters($mockQb, $mockQ);
    }

    public function testApplyListFiltersAlternativeStatus()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class)
            ->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('status', 'bar')
            ->andReturnSelf()
            ->getMock();

        $mockQ = SearchViewList::create(['status' => 'bar']);

        $this->sut->applyListFilters($mockQb, $mockQ);
    }


    public function testApplyListFiltersLocalAuthority()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('licId', '1234')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('busRegStatus', 'foo')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('localAuthorityId', 234)
            ->andReturnSelf();

        $mockQ = BusRegSearchViewList::create(
            [
                'licId' => '1234',
                'busRegStatus' => 'foo',
                'localAuthorityId' => 234
            ]
        );

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
