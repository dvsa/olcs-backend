<?php

/**
 * ComplaintTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;

/**
 * ComplaintTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ComplaintTest extends RepositoryTestCase
{
    public function testApplyListJoins()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(ComplaintRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(QueryBuilder::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);
        $mockQb->shouldReceive('with')->with('complainantContactDetails', 'ccd')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('ccd.person')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('ocComplaints', 'occ')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('occ.operatingCentre', 'oc')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('oc.address')->once()->andReturnSelf();

        $sut->applyListJoins($mockQb);
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

        $mockQb = m::mock(\Dvsa\Olcs\Api\Domain\QueryBuilder::class);
        $sut->shouldReceive('getQueryBuilder')->with()->once()->andReturn($mockQb);
        $mockQb->shouldReceive('with')->with('case', 'ca')->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }
}
