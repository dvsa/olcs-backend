<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Mockery as m;

/**
 * IRHP Permit Stock test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class IrhpPermitStockTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitStock::class);
    }

    public function testGetNextIrhpPermitStockByPermitType()
    {
        $date = new DateTime('2010-01-01');
        $permitType = 'permit_ecmt';
        $expectedResult = m::mock(IrhpPermitStockEntity::class);

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $lteFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $andXFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($lteFunc, $eqFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('lte')
            ->with('?1', 'ips.validTo')
            ->once()
            ->andReturn($lteFunc)
            ->shouldReceive('eq')
            ->with('ipt.name', '?2')
            ->once()
            ->andReturn($eqFunc);

        $queryBuilder->shouldReceive('select')
            ->with('ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitStockEntity::class, 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ips.irhpPermitType', 'ipt')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $date)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $permitType)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ips.validFrom', 'ASC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn([$expectedResult]);

            $this->assertEquals(
                $expectedResult,
                $this->sut->getNextIrhpPermitStockByPermitType($permitType, $date, Query::HYDRATE_ARRAY)
            );
    }

    public function testFetchReadyToPrint()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchReadyToPrint(IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT));

        $expectedQuery = 'BLAH '
            . 'SELECT ips DISTINCT '
            . 'INNER JOIN ips.irhpPermitRanges ipr '
            . 'INNER JOIN ipr.irhpPermits ip '
            . 'AND ip.status IN [[['
                . '"'.IrhpPermitEntity::STATUS_PENDING.'",'
                . '"'.IrhpPermitEntity::STATUS_AWAITING_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_ERROR.'"'
            . ']]] '
            . 'AND ips.irhpPermitType = [['.IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT.']] '
            . 'ORDER BY ips.validFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchReadyToPrintBilateral()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(
            ['RESULTS'],
            $this->sut->fetchReadyToPrint(IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL, 'DE')
        );

        $expectedQuery = 'BLAH '
            . 'SELECT ips DISTINCT '
            . 'INNER JOIN ips.irhpPermitRanges ipr '
            . 'INNER JOIN ipr.irhpPermits ip '
            . 'AND ip.status IN [[['
                . '"'.IrhpPermitEntity::STATUS_PENDING.'",'
                . '"'.IrhpPermitEntity::STATUS_AWAITING_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_ERROR.'"'
            . ']]] '
            . 'AND ips.irhpPermitType = [['.IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL.']] '
            . 'AND ips.country = [[DE]] '
            . 'ORDER BY ips.validFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testGetPermitStockCountByTypeDate()
    {
        $permitCount = 0;
        $permitTypeId = 1;
        $validFrom = '2020-01-01';
        $validTo = '2020-12-31';

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(ips.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitStockEntity::class, 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ips.irhpPermitType = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ips.validFrom = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ips.validTo = ?3')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $permitTypeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $validFrom)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(3, $validTo)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($permitCount);

        $this->assertEquals(
            $permitCount,
            $this->sut->getPermitStockCountByTypeDate($permitTypeId, $validFrom, $validTo)
        );
    }

    public function testFetchAll()
    {
        $irhpPermitStocks = [
            m::mock(IrhpPermitStockEntity::class),
            m::mock(IrhpPermitStockEntity::class),
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $queryBuilder->shouldReceive('getQuery->getResult')
            ->andReturn($irhpPermitStocks);

        $this->mockCreateQueryBuilder($queryBuilder);

        $this->assertEquals(
            $irhpPermitStocks,
            $this->sut->fetchAll()
        );
    }
}
