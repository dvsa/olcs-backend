<?php

/**
 * Bus test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Query\Bus\PreviousVariationByRouteNo;

/**
 * Bus test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(BusRepo::class);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function testFetchUsingId()
    {
        $busRegId = 15;
        $version = 1;

        $command = $this->getCommandWithId($busRegId);

        /** @var QueryBuilder $qb */
        $qb = $this->getMockFetchByIdQueryBuilder(null);

        $this->getFetchByIdQueryBuilder($qb, $busRegId);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($qb);

        $this->em->shouldReceive('getRepository')
            ->with(BusReg::class)
            ->andReturn($repo);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, $version);
    }

    public function testFetchUsingIdWithResults()
    {
        $busRegId = 15;
        $version = 1;

        $command = $this->getCommandWithId($busRegId);

        $result = m::mock(BusReg::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = $this->getMockFetchByIdQueryBuilder($results);

        $this->getFetchByIdQueryBuilder($qb, $busRegId);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($qb);

        $this->em->shouldReceive('getRepository')
            ->with(BusReg::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, $version);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, $version);
    }

    /**
     * @param $qb
     * @return m\MockInterface
     */
    public function getMockRepo($qb)
    {
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        return $repo;
    }

    /**
     * @param mixed $results
     * @return m\MockInterface
     */
    public function getMockFetchByIdQueryBuilder($results)
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        return $qb;
    }

    /**
     * @param int $busRegId
     * @return m\MockInterface
     */
    public function getCommandWithId($busRegId)
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($busRegId);

        return $command;
    }

    /**
     * @param $qb
     * @param int $busRegId
     */
    public function getFetchByIdQueryBuilder($qb, $busRegId)
    {
        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('busNoticePeriod')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('busServiceTypes')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('trafficAreas')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('localAuthoritys')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('subsidised')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('otherServices')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with($busRegId);
    }

    public function testApplyListFilters()
    {
        $sut = m::mock(BusRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $variationNo = 11;
        $routeNo = 22;

        $mockQuery = m::mock(PreviousVariationByRouteNo::class);
        $mockQuery->shouldReceive('getRouteNo')
            ->andReturn($routeNo)
            ->once()
            ->shouldReceive('getVariationNo')
            ->andReturn($variationNo)
            ->once()
            ->getMock();

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->lt')->with('m.variationNo', ':byVariationNo')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byVariationNo', $variationNo)->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.routeNo', ':byRouteNo')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byRouteNo', $routeNo)->once()->andReturnSelf();

        $sut->applyListFilters($mockQb, $mockQuery);
    }

    /**
     * Tests applyListJoins
     */
    public function testApplyListJoins()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(BusRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(QueryBuilder::class);

        $mockQb->shouldReceive('modifyQuery')->andReturnSelf();
        $mockQb->shouldReceive('with')->with('busNoticePeriod')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('status')->once()->andReturnSelf();
        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $sut->applyListJoins($mockQb);
    }

    /**
     * Test fetchWithTxcInboxListForOrganisation
     */
    public function testFetchWithTxcInboxListForOrganisation()
    {
        $busRegId = 15;

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getId')
            ->andReturn($busRegId);

        $results = 'results';

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($qb);
        $this->em->shouldReceive('getRepository')
            ->with(BusReg::class)
            ->andReturn($repo);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->with($busRegId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once();

        $qb->shouldReceive('addSelect');
        $qb->shouldReceive('leftJoin')
        ->with(
            m::type('string'),
            m::type('string'),
            'WITH',
            matchesPattern('/localAuthority IS NULL AND t.organisation =/')
        );

        $this->sut->fetchWithTxcInboxListForOrganisation($query, Query::HYDRATE_OBJECT);
    }

    /**
     * Test fetchWithTxcInboxListForLocalAuthority where LA exists
     */
    public function testFetchWithTxcInboxListForLocalAuthority()
    {
        $busRegId = 15;

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getId')
            ->andReturn($busRegId);

        $results = 'results';

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($qb);
        $this->em->shouldReceive('getRepository')
            ->with(BusReg::class)
            ->andReturn($repo);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->with($busRegId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once();

        $qb->shouldReceive('addSelect');
        $qb->shouldReceive('expr')->andReturnSelf()->shouldReceive('eq')->andReturn('t.localAuthority = 1');
        $qb->shouldReceive('leftJoin')
            ->with(
                m::type('string'),
                m::type('string'),
                'WITH',
                matchesPattern('/localAuthority = 1/')
            )
            ->andReturnSelf()
            ->shouldReceive('setParameter')->with('localAuthority', 1);

        $this->sut->fetchWithTxcInboxListForLocalAuthority($query, 1, Query::HYDRATE_OBJECT);
    }

    /**
     * Test fetchWithTxcInboxListForLocalAuthority where empty LA
     */
    public function testFetchWithTxcInboxListForEmptyLocalAuthority()
    {
        $busRegId = 15;

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getId')
            ->andReturn($busRegId);

        $results = 'results';

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($qb);
        $this->em->shouldReceive('getRepository')
            ->with(BusReg::class)
            ->andReturn($repo);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->with($busRegId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once();

        $qb->shouldReceive('addSelect');
        $qb->shouldReceive('expr')->andReturnSelf()->shouldReceive('isNull')->andReturn('t.localAuthority IS NULL');
        $qb->shouldReceive('leftJoin')
            ->with(
                m::type('string'),
                m::type('string'),
                'WITH',
                matchesPattern('/localAuthority IS NULL/')
            )
            ->andReturnSelf()
            ->shouldReceive('setParameter')->with('localAuthority', 1);

        $this->sut->fetchWithTxcInboxListForLocalAuthority($query, null, Query::HYDRATE_OBJECT);
    }
}
