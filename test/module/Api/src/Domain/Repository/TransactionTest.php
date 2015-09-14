<?php

/**
 * Transaction test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Transaction as TransactionRepo;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Mockery as m;

/**
 * Transaction test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TransactionRepo::class);
    }

    public function testFetchByReference()
    {
        $ref = 'OLCS-1234-ABCD';

        $result = m::mock(Transaction::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $where = m::mock();
        $qb->shouldReceive('expr->eq')
            ->with('t.reference', ':reference')
            ->andReturn($where);
        $qb
            ->shouldReceive('andWhere')
            ->with($where)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('reference', $ref)
            ->once()
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            //->withAnyArgs()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('t')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Transaction::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchByReference($ref, Query::HYDRATE_OBJECT, 1);
    }
}
