<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as IrhpPermitRangeEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Mockery as m;

/**
 * IRHP Candidate Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpCandidatePermit::class);
    }

    public function testGetCountLackingRandomisedScore()
    {
        $candidatePermitCount = 35;
        $stockId = 5;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(icp.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with("ipa.status = '" . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION . "'")
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('icp.randomizedScore is null')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($candidatePermitCount);

        $this->assertEquals(
            $candidatePermitCount,
            $this->sut->getCountLackingRandomisedScore($stockId)
        );
    }

    public function testGetScoreOrderedUnderConsiderationIdsBySector()
    {
        $stockId = 6;
        $sectorsId = 8;
        $expectedResult = [18, 24, 25, 31, 34];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('icp.id')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(ipa.sectors) = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with("ipa.status = '" . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION . "'")
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('icp.randomizedScore', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $sectorsId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->getScoreOrderedUnderConsiderationIdsBySector($stockId, $sectorsId)
        );
    }

    public function testGetSuccessfulDaCount()
    {
        $successfulDaCount = 35;
        $stockId = 8;
        $jurisdictionId = 12;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(icp.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('icp.successful = 1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(l.trafficArea) = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $jurisdictionId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($successfulDaCount);

        $this->assertEquals(
            $successfulDaCount,
            $this->sut->getSuccessfulDaCount($stockId, $jurisdictionId)
        );
    }

    public function testGetUnsuccessfulScoreOrderedUnderConsiderationIds()
    {
        $candidatePermitIds = [35, 37, 41];
        $stockId = 3;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('icp.id')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('icp.successful = 0')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with("ipa.status = '" . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION . "'")
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('icp.randomizedScore', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($candidatePermitIds);

        $this->assertEquals(
            $candidatePermitIds,
            $this->sut->getUnsuccessfulScoreOrderedUnderConsiderationIds($stockId)
        );
    }

    public function testGetSuccessfulCount()
    {
        $stockId = 7;
        $successfulCount = 15;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(icp)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('icp.successful = true')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($successfulCount);

        $this->assertEquals(
            $successfulCount,
            $this->sut->getSuccessfulCount($stockId)
        );
    }

    public function testMarkAsSuccessful()
    {
        $candidatePermitIds = [3, 8, 12, 16];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $query = m::mock(AbstractQuery::class);
        $query->shouldReceive('execute')
            ->withNoArgs()
            ->once();

        $queryBuilder->shouldReceive('update')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('set')
            ->with('icp.successful', 1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('icp.id in (?1)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $candidatePermitIds)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery')
            ->withNoArgs()
            ->once()
            ->andReturn($query);

        $this->sut->markAsSuccessful($candidatePermitIds);
    }

    public function testGetIrhpCandidatePermitsForScoring()
    {
        $irhpPermitStockId = 1;
        $licenceTypes = ['ltyp_r', 'ltyp_si'];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipw.irhpPermitStock', 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ips.id = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipa.status = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('l.licenceType IN (?3)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $irhpPermitStockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, EcmtPermitApplication::STATUS_UNDER_CONSIDERATION)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(3, $licenceTypes)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn(null);

        $this->assertEquals(null, $this->sut->getIrhpCandidatePermitsForScoring($irhpPermitStockId));
    }

    public function testGetSuccessfulScoreOrdered()
    {
        $stockId = 7;

        $expectedResult = [
            m::mock(IrhpCandidatePermitEntity::class),
            m::mock(IrhpCandidatePermitEntity::class),
            m::mock(IrhpCandidatePermitEntity::class),
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('icp.successful = 1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('icp.randomizedScore', 'DESC')
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
            $this->sut->getSuccessfulScoreOrdered($stockId)
        );
    }

    public function testUpdateRange()
    {
        $candidatePermitId = 62;
        $range = m::mock(IrhpPermitRangeEntity::class);

        $query = m::mock(AbstractQuery::class);
        $query->shouldReceive('execute')
            ->withNoArgs()
            ->once();

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('update')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('set')
            ->with('icp.irhpPermitRange', $range)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('icp.id = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $candidatePermitId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $this->sut->updateRange($candidatePermitId, $range);
    }

    public function testResetScoring()
    {
        $stockId = 7;

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('execute')
            ->once();

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'update irhp_candidate_permit ' .
                'set successful = 0, irhp_permit_range_id = NULL ' .
                'where irhp_permit_application_id in (' .
                '    select id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ')',
                ['stockId' => $stockId]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->sut->resetScoring($stockId);
    }

    public function testFetchAllScoredForStock()
    {
        $irhpPermitStockId = 1;

        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('select')
            ->with('icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipw.irhpPermitStock', 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ips.id = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipa.status = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('icp.successful', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('addOrderBy')
            ->with('icp.randomizedScore', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $irhpPermitStockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, EcmtPermitApplication::STATUS_UNDER_CONSIDERATION)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn(null);

        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($this->queryBuilder);

        $this->assertEquals(null, $this->sut->fetchAllScoredForStock($irhpPermitStockId));
    }
}
