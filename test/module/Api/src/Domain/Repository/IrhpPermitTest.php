<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByIrhpId;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrint;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrintConfirm;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Mockery as m;
use PDO;

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

    public function testGetAssignedPermitNumbersByRange()
    {
        $rangeId = 45;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $unflattenedPermitNumbers = [
            ['permitNumber' => 4],
            ['permitNumber' => 5],
            ['permitNumber' => 7],
            ['permitNumber' => 8],
        ];

        $flattenedPermitNumbers = [4, 5, 7, 8];

        $queryBuilder->shouldReceive('select')
            ->with('ip.permitNumber')
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
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($unflattenedPermitNumbers);

        $this->assertEquals(
            $flattenedPermitNumbers,
            $this->sut->getAssignedPermitNumbersByRange($rangeId)
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
            . 'AND m.status IN [[["irhp_permit_pending","irhp_permit_awaiting_printing","irhp_permit_printing","irhp_permit_printed","irhp_permit_error","irhp_permit_issued"]]] '
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

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf();

        $query = GetListByLicence::create(['licence' => 7, 'page' => 1, 'limit' => 10]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'INNER JOIN ipa.irhpApplication ia '
            . 'INNER JOIN ipa.irhpPermitWindow ipw '
            . 'INNER JOIN ipw.irhpPermitStock ips '
            . 'INNER JOIN ips.country ipc '
            . 'AND ia.licence = [[7]] '
            . 'AND ipa.irhpApplication IS NOT NULL '
            . 'ORDER BY ipc.countryDesc ASC '
            . 'ORDER BY m.expiryDate ASC '
            . 'ORDER BY ipa.id ASC '
            . 'ORDER BY m.permitNumber ASC';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testGetLivePermitCountsGroupedByStock()
    {
        $licenceId = 47;

        $livePermitCounts = [
            [
                'irhpPermitStockId' => 7,
                'irhpPermitCount' => 8
            ],
            [
                'irhpPermitStockId' => 5,
                'irhpPermitCount' => 12
            ]
        ];

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('fetchAll')
            ->once()
            ->andReturn($livePermitCounts);

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'select ips.id AS irhpPermitStockId, ' .
                'count(ip.id) AS irhpPermitCount ' .
                'from irhp_permit ip ' .
                'inner join irhp_permit_application ipa ON ip.irhp_permit_application_id = ipa.id ' .
                'and ip.status in (?) ' .
                'inner join irhp_application ia ON ipa.irhp_application_id = ia.id ' .
                'inner join irhp_permit_window ipw ON ipa.irhp_permit_window_id = ipw.id ' .
                'inner join irhp_permit_stock ips ON ipw.irhp_permit_stock_id = ips.id ' .
                'where ia.licence_id = ? ' .
                'group BY ips.id',
                [
                    [
                        IrhpPermitEntity::STATUS_PENDING,
                        IrhpPermitEntity::STATUS_AWAITING_PRINTING,
                        IrhpPermitEntity::STATUS_PRINTING,
                        IrhpPermitEntity::STATUS_PRINTED
                    ],
                    $licenceId
                ],
                [
                    Connection::PARAM_STR_ARRAY,
                    PDO::PARAM_INT
                ]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->assertEquals(
            $livePermitCounts,
            $this->sut->getLivePermitCountsGroupedByStock($licenceId)
        );
    }

    public function testFetchListByIrhpId()
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

        $query = GetListByIrhpId::create(['irhpApplication' => 2, 'page' => 1, 'limit' => 10]);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND ipa.irhpApplication = [[2]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
