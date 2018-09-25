<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as IrhpPermitRangeEntity;
use Mockery as m;

/**
 * IRHP Permit Range test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitRangeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitRange::class);
    }

    public function testGetCombinedRangeSize()
    {
        $combinedRangeSize = 1002;
        $stockId = 5;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('SUM((ipr.toNo - ipr.fromNo) + 1)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitRangeEntity::class, 'ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ipr.ssReserve = false')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipr.lostReplacement = false')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($combinedRangeSize);

        $this->assertEquals(
            $combinedRangeSize,
            $this->sut->getCombinedRangeSize($stockId)
        );
    }

    public function testGetByStockId()
    {
        $expectedResult = [
            IrhpPermitRangeEntity::class,
            IrhpPermitRangeEntity::class,
            IrhpPermitRangeEntity::class,
        ];

        $stockId = 7;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitRangeEntity::class, 'ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ipr.ssReserve = false')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipr.lostReplacement = false')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->getByStockId($stockId)
        );
    }
}
