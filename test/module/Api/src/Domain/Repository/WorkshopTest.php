<?php

/**
 * Workshop test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Workshop as WorkshopRepo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Licence\Workshop;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Doctrine\DBAL\LockMode;

/**
 * Workshop test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class WorkshopTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(WorkshopRepo::class);
    }

    public function testFetchUsingId()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(null);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Workshop::class)
            ->andReturn($repo);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchUsingIdWithResults()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        $result = m::mock(Workshop::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Workshop::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, 1);
    }
}
