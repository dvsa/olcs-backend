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
    public function testApplyListFilters()
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
}
