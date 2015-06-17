<?php

/**
 * Payment test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Api\Entity\Fee\Payment;
use Mockery as m;

/**
 * Payment test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(PaymentRepo::class);
    }

    public function testFetchByReference()
    {
        $ref = 'OLCS-1234-ABCD';

        $result = m::mock(Payment::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $where = m::mock();
        $qb->shouldReceive('expr->eq')
            ->with('p.guid', ':reference')
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
            ->with('p')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Payment::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchByReference($ref, Query::HYDRATE_OBJECT, 1);
    }
}
