<?php

/**
 * LegacyOffence Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Legacy\LegacyOffence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\LegacyOffence as Repo;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * LegacyOffence Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class LegacyOffenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchCaseLegacyOffenceUsingId()
    {
        $id = 99;
        $case = 24;
        $mockResult = [0 => 'result'];

        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($id);
        $command->shouldReceive('getCase')
            ->andReturn($case);

        /** @var Expr $expr */
        $expr = m::mock(QueryBuilder::class);
        $expr->shouldReceive('eq')
            ->with(m::type('string'), ':byCase')
            ->andReturnSelf();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr')
            ->andReturn($expr);

        $qb->shouldReceive('setParameter')
            ->with('byCase', $case)
            ->andReturnSelf();

        $qb->shouldReceive('andWhere')
            ->with($expr)
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefData')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('case')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('createdBy')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('lastModifiedBy')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with($id);

        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($mockResult);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(LegacyOffence::class)
            ->andReturn($repo);

        $result = $this->sut->fetchCaseLegacyOffenceUsingId($command, Query::HYDRATE_OBJECT);

        $this->assertEquals($result, $mockResult[0]);
    }

    public function testBuildDefaultListQuery()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(ComplaintRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);
        $mockQb = m::mock(\Dvsa\Olcs\Api\Domain\QueryBuilder::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $mockQb->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $mockQb->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('case', 'ca')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('complainantContactDetails', 'ccd')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('ccd.person')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('ocComplaints', 'occ')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('occ.operatingCentre', 'oc')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('oc.address')->once()->andReturnSelf();

        $sut->buildDefaultListQuery($qb, $query);
    }

    public function testApplyFiltersCase()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(ComplaintRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);

        $query->shouldReceive('getCase')->with()->andReturn(213);
        $query->shouldReceive('getIsCompliance')->with()->andReturn(null);
        $query->shouldReceive('getLicence')->with()->andReturn(null);

        $qb->shouldReceive('expr->eq')->with('m.case', ':byCase')->once()->andReturn('EXPR');
        $qb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('byCase', 213)->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }

    public function testApplyFiltersCompliance()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(ComplaintRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);

        $query->shouldReceive('getCase')->with()->andReturn(null);
        $query->shouldReceive('getIsCompliance')->with()->andReturn(324);
        $query->shouldReceive('getLicence')->with()->andReturn(null);

        $qb->shouldReceive('expr->eq')->with('m.isCompliance', ':isCompliance')->once()->andReturn('EXPR');
        $qb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('isCompliance', 324)->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }

    public function testApplyFiltersLicence()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(ComplaintRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);

        $query->shouldReceive('getCase')->with()->andReturn(null);
        $query->shouldReceive('getIsCompliance')->with()->andReturn(null);
        $query->shouldReceive('getLicence')->with()->andReturn(33);

        $qb->shouldReceive('expr->eq')->with('ca.licence', ':licence')->once()->andReturn('EXPR');
        $qb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', 33)->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }
}
