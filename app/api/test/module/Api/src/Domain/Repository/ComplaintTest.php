<?php

/**
 * Complaint Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as Repo;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * Complaint Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ComplaintTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchUsingCaseId()
    {
        $id = 99;
        $case = 24;
        $isCompliance = 1;
        $mockResult = [0 => 'result'];

        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($id);
        $command->shouldReceive('getCase')
            ->andReturn($case);
        $command->shouldReceive('getIsCompliance')
            ->andReturn($isCompliance);

        /** @var Expr $expr */
        $expr = m::mock(QueryBuilder::class);
        $expr->shouldReceive('eq')
            ->with(m::type('string'), ':byCase')
            ->andReturnSelf();

        $expr->shouldReceive('eq')
            ->with(m::type('string'), ':isCompliance')
            ->andReturnSelf();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr')
            ->andReturn($expr);

        $qb->shouldReceive('setParameter')
            ->with('byCase', $case)
            ->andReturnSelf();

        $qb->shouldReceive('setParameter')
            ->with('isCompliance', $isCompliance)
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
            ->shouldReceive('withPersonContactDetails')
            ->once()
            ->with('complainantContactDetails')
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
            ->with(Complaint::class)
            ->andReturn($repo);

        $result = $this->sut->fetchUsingCaseId($command, Query::HYDRATE_OBJECT);

        $this->assertEquals($result, $mockResult[0]);
    }
}
