<?php

/**
 * TransportManagerApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as Repo;
use Mockery as m;

/**
 * TransportManagerApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchWithContactDetailsByApplication()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('with')->with('tma.transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.homeCd', 'hcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.address', 'hadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hadd.countryCode')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.person', 'hp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.workCd', 'wcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wcd.address', 'wadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wadd.countryCode')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('tma.application', ':applicationId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('applicationId', 834)->once();
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchWithContactDetailsByApplication(834));
    }

    public function testFetchDetails()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.application', 'a')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.otherLicences', 'ol')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ol.role')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.goodsOrPsv', 'gop')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.licence')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.status')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('with')->with('tma.transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.homeCd', 'hcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.address', 'hadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hadd.countryCode')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.person', 'hp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.workCd', 'wcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wcd.address', 'wadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wadd.countryCode')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertSame('RESULT', $this->sut->fetchDetails(834));
    }

    public function testFetchDetailsEmpty()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.application', 'a')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.otherLicences', 'ol')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ol.role')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.goodsOrPsv', 'gop')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.licence')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.status')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('with')->with('tma.transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.homeCd', 'hcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.address', 'hadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hadd.countryCode')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('hcd.person', 'hp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.workCd', 'wcd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wcd.address', 'wadd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('wadd.countryCode')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn([]);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class);

        $this->sut->fetchDetails(834);
    }

    public function testFetchWithOperatingCentres()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.operatingCentres', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertSame('RESULT', $this->sut->fetchWithOperatingCentres(834));
    }

    public function testFetchWithOperatingCentresEmpty()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tma.operatingCentres', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn([]);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class);

        $this->assertSame('RESULT', $this->sut->fetchWithOperatingCentres(834));
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListJoins()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQb = m::mock();

        $sut->shouldReceive('getQueryBuilder')->with()->once()->andReturn($mockQb);
        $mockQb->shouldReceive('with')->with('application', 'a')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('a.licence', 'l')->once()->andReturnSelf();

        $sut->applyListJoins($mockDqb);
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListFiltersUser()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockDqb->shouldReceive('join')->with('tma.transportManager', 'tm')->once();
        $mockDqb->shouldReceive('join')->with('tm.users', 'u')->once();
        $mockDqb->shouldReceive('expr->eq')->with('u.id', ':user')->once()->andReturn('EXPR');
        $mockDqb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $mockDqb->shouldReceive('setParameter')->with('user', 73)->once();

        $query = \Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetList::create(['user' => 73]);
        $sut->applyListFilters($mockDqb, $query);
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListFiltersApplication()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockDqb->shouldReceive('expr->eq')->with('tma.application', ':application')->once()->andReturn('EXPR');
        $mockDqb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $mockDqb->shouldReceive('setParameter')->with('application', 73)->once();

        $query = \Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetList::create(['application' => 73]);
        $sut->applyListFilters($mockDqb, $query);
    }

    /**
     * Mock SUT so that can just test the protected method
     */
    public function testApplyListFiltersTransportManager()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDqb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockDqb->shouldReceive('expr->eq')->with('tma.transportManager', ':transportManager')->once()
            ->andReturn('EXPR');
        $mockDqb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $mockDqb->shouldReceive('setParameter')->with('transportManager', 73)->once();

        $query = \Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetList::create(['transportManager' => 73]);
        $sut->applyListFilters($mockDqb, $query);
    }

    public function testFetchForTransportManager()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmType', 'tmt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('application', 'a')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.licence', 'al')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('al.organisation', 'alo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.status', 'ast')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmApplicationStatus', 'tmast')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('tma.transportManager', ':transportManager')->once()->andReturn('tm');
        $mockQb->shouldReceive('where')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('transportManager', 1)->once();

        $mockQb->shouldReceive('expr->neq')->with('tma.action', ':action')->once()->andReturn('ac');
        $mockQb->shouldReceive('andWhere')->with('ac')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('action', 'D')->once();

        $conditions = [
            'a.status = :status0',
            'a.status = :status1'
        ];
        $mockQb->shouldReceive('expr->orX->addMultiple')->with($conditions)->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status0', 'a')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status1', 'b')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertEquals(['RESULT'], $this->sut->fetchForTransportManager(1, 'a,b'));
    }

    public function testFetchByTmAndApplication()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $mockQb->shouldReceive('expr->eq')->with('tma.transportManager', ':tmId')->once()->andReturn('EXPR1');
        $mockQb->shouldReceive('andWhere')->with('EXPR1')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('tmId', 1)->once();

        $mockQb->shouldReceive('expr->eq')->with('tma.application', ':applicationId')->once()->andReturn('EXPR2');
        $mockQb->shouldReceive('andWhere')->with('EXPR2')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('applicationId', 2)->once();

        $mockQb->shouldReceive('expr->neq')->with('tma.action', ':action')->once()->andReturn('EXPR3');
        $mockQb->shouldReceive('andWhere')->with('EXPR3')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('action', 'D')->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');
        $this->assertEquals('RESULT', $this->sut->fetchByTmAndApplication(1, 2, true));
    }


    public function testFetchForResponsibilities()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tma')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('application', 'a')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmType', 'tmty')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.licence', 'al')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('al.organisation', 'alo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('a.status', 'ast')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tm.tmType', 'tmt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('tmApplicationStatus', 'tmast')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn(['RESULT']);
        $this->assertEquals(['RESULT'], $this->sut->fetchForResponsibilities(1));
    }
}
