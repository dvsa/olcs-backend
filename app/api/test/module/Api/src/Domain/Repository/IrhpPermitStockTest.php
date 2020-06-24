<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
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
    public function setUp(): void
    {
        $this->setUpSut(IrhpPermitStock::class);
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
            ->shouldReceive('andWhere')
            ->with('ips.id != ?4')
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
            ->shouldReceive('setParameter')
            ->with(4, 0)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($permitCount);

        $this->assertEquals(
            $permitCount,
            $this->sut->getPermitStockCountByTypeDate($permitTypeId, $validFrom, $validTo, 0)
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

    public function testFetchOpenBilateralStocksByCountry()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $now = new DateTime();

        $this->assertEquals(
            ['RESULTS'],
            $this->sut->fetchOpenBilateralStocksByCountry(Country::ID_NORWAY, $now)
        );

        $iso8601String = $now->format(DateTime::ISO8601);

        $expectedQuery = 'BLAH '.
        'SELECT ips '.
        'INNER JOIN ips.irhpPermitType ipt '.
        'INNER JOIN ips.irhpPermitWindows ipw '.
        'INNER JOIN ips.country c '.
        'AND ips.country = [[NO]] '.
        "AND ipw.startDate <= [[$iso8601String]] ".
        "AND ipw.endDate > [[$iso8601String]] AND ipt.id = [[4]]";

        $this->assertEquals($expectedQuery, $this->query);
    }
}
