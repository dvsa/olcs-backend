<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrint;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrintConfirm;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence;
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
            ->shouldReceive('andWhere')
            ->with('ipr.ssReserve = false')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipr.lostReplacement = false')
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

    public function testFetchListForReadyToPrintWithStock()
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

        $query = ReadyToPrint::create(['irhpPermitStock' => 100]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'INNER JOIN m.irhpPermitRange ipr '
            . 'INNER JOIN ipr.irhpPermitStock ips '
            . 'AND ips.id = [[100]] '
            . 'AND m.status IN [[['
                . '"'.IrhpPermitEntity::STATUS_PENDING.'",'
                . '"'.IrhpPermitEntity::STATUS_AWAITING_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_PRINTING.'",'
                . '"'.IrhpPermitEntity::STATUS_ERROR.'"'
            . ']]] '
            . 'ORDER BY m.permitNumber ASC';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForReadyToPrintConfirm()
    {
        $this->setUpSut(IrhpPermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('paginate')->andReturnSelf();

        $query = ReadyToPrintConfirm::create(['ids' => [1, 2, 3]]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND m.id IN [[[1,2,3]]] '
            . 'ORDER BY m.permitNumber ASC';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByNumberAndRange()
    {
        $permitNumber = 1500;
        $rangeId = 7;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('ip')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitEntity::class, 'ip')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ip.permitNumber = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ip.irhpPermitRange = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $permitNumber)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $rangeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->execute')
            ->once()
            ->andReturn([]);

        $this->assertEquals(
            [],
            $this->sut->fetchByNumberAndRange($permitNumber, $rangeId)
        );
    }

    public function testFetchListForDashboard()
    {
        $this->setUpSut(IrhpPermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        //$qb = $this->createMockQb('BLAH');
        //$this->mockCreateQueryBuilder($qb);

        $qb = m::mock(QueryBuilder::class);
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('modifyQuery')->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpPermitWindow', 'ipw')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipw.irhpPermitStock', 'ips')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ips.country', 'ipc')->once()->andReturnSelf()
            ->shouldReceive('orderBy')->with('ipc.countryDesc', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('addOrderBy')->with('m.expiryDate', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('addOrderBy')->with('ipa.id', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('addOrderBy')->with('m.permitNumber', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf();

        $query = GetListByLicence::create(['licence' => 7, 'page' => 1, 'limit' => 10]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'ORDER BY ipc.countryDesc ASC, m.expiryDate ASC, ipa.id ASC, m.permitNumber ASC';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
