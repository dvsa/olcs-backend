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
            ->shouldReceive('byId')
            ->once()
            ->with($busRegId);
    }

}
