<?php

/**
 * Queue test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * Queue test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class QueueTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(QueueRepo::class, true);
    }

    public function testGetNextItem()
    {
        $typeId = 'foo';

        $item = m::mock(QueueEntity::class)->makePartial();
        $results = [$item];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $where = m::mock();
        // stub out complext expression builder calls
        $qb->shouldReceive('expr->eq');
        $qb->shouldReceive('expr->orX');
        $qb->shouldReceive('expr->lte');
        $qb->shouldReceive('expr->isNull');

        $now = new DateTime();
        $qb
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('statusId', QueueEntity::STATUS_QUEUED)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(
                'processAfter',
                m::on(
                    function (DateTime $value) use ($now) {
                        return $now == $value;
                    }
                )
            )
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('typeId', $typeId)
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->once()
            ->with(1)
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('order')
            ->with('createdOn', 'ASC')
            ->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn($results);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(QueueEntity::class)
            ->andReturn($repo);

        $ref = m::mock(RefData::class)->makePartial();
        $this->sut->shouldReceive('getRefdataReference')
            ->with(QueueEntity::STATUS_PROCESSING)
            ->once()
            ->andReturn($ref);

        $item
            ->shouldReceive('incrementAttempts')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setStatus')
            ->once()
            ->with($ref)
            ->andReturnSelf();

        $this->sut
            ->shouldReceive('save')
            ->with($item)
            ->once();

        $this->sut->getNextItem($typeId);
    }
}
