<?php

/**
 * TransportManagerLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as Repo;
use Mockery as m;

/**
 * TransportManagerLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerLicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchWithContactDetailsByLicence()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tml')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $mockQb->shouldReceive('join')->with('tml.transportManager', 'tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('join')->with('tm.homeCd', 'hcd')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('hcd')->once()->andReturnSelf();
        $mockQb->shouldReceive('join')->with('hcd.person', 'p')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('p')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('tml.licence', ':licenceId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licenceId', 834)->once();
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchWithContactDetailsByLicence(834));
    }

    public function testFetchForTransportManager()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tml')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmType', 'tmt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.status', 'ls')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'oc')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('tml.transportManager', ':transportManager')->once()->andReturn('tm');
        $mockQb->shouldReceive('where')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('transportManager', 1)->once();

        $conditions = [
            'l.status = :status0',
            'l.status = :status1'
        ];
        $mockQb->shouldReceive('expr->orX->addMultiple')->with($conditions)->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status0', 'a')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status1', 'b')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertEquals(['RESULT'], $this->sut->fetchForTransportManager(1, 'a,b'));
    }

    public function testFetchForResponsibilities()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tml')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.status', 'lst')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.tmType', 'tmty')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmType', 'tmt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn(['RESULT']);
        $this->assertEquals(['RESULT'], $this->sut->fetchForResponsibilities(1));
    }

    public function testFetchByTmAndLicence()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tml')->once()->andReturn($mockQb);

        $mockQb->shouldReceive('expr->eq')->with('tml.transportManager', ':tmId')->once()->andReturn('EXPR1');
        $mockQb->shouldReceive('andWhere')->with('EXPR1')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('tmId', 1)->once();

        $mockQb->shouldReceive('expr->eq')->with('tml.licence', ':licenceId')->once()->andReturn('EXPR2');
        $mockQb->shouldReceive('andWhere')->with('EXPR2')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licenceId', 2)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');
        $this->assertEquals('RESULT', $this->sut->fetchByTmAndLicence(1, 2));
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListFiltersLicence()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockDqb->shouldReceive('expr->eq')->with('tml.licence', ':licence')->once()
            ->andReturn('EXPR');
        $mockDqb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $mockDqb->shouldReceive('setParameter')->with('licence', 73)->once();

        $query = \Dvsa\Olcs\Transfer\Query\TransportManagerLicence\GetList::create(['licence' => 73]);
        $sut->applyListFilters($mockDqb, $query);
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListFiltersTransportManager()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockDqb->shouldReceive('expr->eq')->with('tml.transportManager', ':transportManager')->once()
            ->andReturn('EXPR');
        $mockDqb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $mockDqb->shouldReceive('setParameter')->with('transportManager', 73)->once();

        $query = \Dvsa\Olcs\Transfer\Query\TransportManagerLicence\GetList::create(['transportManager' => 73]);
        $sut->applyListFilters($mockDqb, $query);
    }
}
