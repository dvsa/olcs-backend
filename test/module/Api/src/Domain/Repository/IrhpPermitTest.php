<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrint;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Mockery as m;

/**
 * IRHP Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermit::class);
    }

    public function testGetPermitCount()
    {
        $permitCount = 744;
        $stockId = 5;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(ip.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitEntity::class, 'ip')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ip.irhpPermitRange', 'ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($permitCount);

        $this->assertEquals(
            $permitCount,
            $this->sut->getPermitCount($stockId)
        );
    }

    public function testGetPermitCountByRange()
    {
        $permitCount = 200;
        $rangeId = 3;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(ip.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitEntity::class, 'ip')
            ->once()
            ->andReturnSelf()
           ->shouldReceive('where')
            ->with('IDENTITY(ip.irhpPermitRange) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $rangeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($permitCount);

        $this->assertEquals(
            $permitCount,
            $this->sut->getPermitCountByRange($rangeId)
        );
    }

    public function testFetchListForValidEcmtPermits()
    {
        $this->setUpSut(IrhpPermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf();

        $query = ValidEcmtPermits::create(['id' => 'ID']);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND ipa.ecmtPermitApplication = [[ID]] '
            . 'ORDER BY m.permitNumber DESC';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForReadyToPrint()
    {
        $this->setUpSut(IrhpPermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf();

        $query = ReadyToPrint::create([]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND m.status IN [[['
                . '"'.IrhpPermitEntity::STATUS_PENDING.'",'
                . '"'.IrhpPermitEntity::STATUS_AWAITING_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_ERROR.'"'
            . ']]] '
            . 'ORDER BY m.permitNumber ASC';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByPermitNumber()
    {

        $this->setUpSut(IrhpPermit::class, true);
        $permitNumber = 200;
        $permitRange = 7;

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $eqFunc1 = m::mock(Func::class);
        $eqFunc2 = m::mock(Func::class);
        $expr = m::mock(Expr::class);
        $expr->shouldReceive('eq')
            ->with('irhp_permit.permitNumber', ':permitNumber')
            ->andReturn($eqFunc1);
        $expr->shouldReceive('eq')
            ->with('irhp_permit.permitRange', ':permitRange')
            ->andReturn($eqFunc2);

        $collection = [m::mock(IrhpPermitEntity::class)];

        $qb->shouldReceive('andWhere')
            ->with($eqFunc1)
            ->andReturnSelf();

        $qb->shouldReceive('andWhere')
            ->with($eqFunc2)
            ->andReturnSelf();

        $qb->shouldReceive('setParameter')
            ->with('permitNumber', $permitNumber)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('permitRange', $permitRange)
            ->andReturnSelf()
            ->shouldReceive('getQuery->execute')
            ->once()
            ->andReturn($collection);

        $this->assertEquals(
            $collection,
            $this->sut->fetchByPermitNumberAndRange($permitNumber, $permitRange)
        );
    }
}
