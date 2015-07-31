<?php

/**
 * UserTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Mockery as m;

/**
 * UserTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UserTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testBuildDefaultQuery()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('contactDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.person')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertSame('RESULT', $this->sut->fetchById(834));
    }

    /**
     * Had to mock SUT as the fetchList method uses Paginator which has proving time consuming to mock
     */
    public function testApplyListFilters()
    {
        $sut = m::mock(Repo::class);

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $mockQi->shouldReceive('getOrganisation')->with()->twice()->andReturn(43);

        $mockQb->shouldReceive('join')
            ->with('u.organisationUsers', 'ou', \Doctrine\ORM\Query\Expr\Join::WITH, 'ou.organisation = :organisation')
            ->once();
        $mockQb->shouldReceive('setParameter')->with('organisation', 43)->once();

        $sut->applyListFilters($mockQb, $mockQi);
    }

    /**
     * Had to mock SUT as the fetchList method uses Paginator which has proving time consuming to mock
     */
    public function testBuildDefaultListQuery()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $mockQb->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $mockQb->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('contactDetails', 'cd')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('cd.person')->once()->andReturnSelf();

        $sut->buildDefaultListQuery($mockQb, $mockQi);
    }

    public function testFetchForTma()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('u.contactDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.person', 'cdp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('u.transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchForTma(1));
    }
}
