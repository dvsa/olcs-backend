<?php

/**
 * ProposeToRevoke Repo test
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke as Repo;

/**
 * ProposeToRevoke Repo test
 */
class ProposeToRevokeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchProposeToRevokeUsingCase()
    {
        $case = 24;
        $mockResult = [0 => 'result'];

        $command = m::mock(QueryInterface::class);
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
            ->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($mockResult);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(ProposeToRevoke::class)
            ->andReturn($repo);

        $result = $this->sut->fetchProposeToRevokeUsingCase($command, Query::HYDRATE_OBJECT);

        $this->assertEquals($result, $mockResult[0]);
    }
}
