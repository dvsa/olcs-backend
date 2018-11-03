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

    public function testGetScoreOrderedIdsBySectorInScope()
    {
        $stockId = 6;
        $sectorsId = 8;

        $nonNormalisedResult = [
            ['id' => 18],
            ['id' => 24],
            ['id' => 25],
            ['id' => 31],
            ['id' => 34],
        ];

        $normalisedResult = [18, 24, 25, 31, 34];

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
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(epa.sectors) = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('epa.inScope = 1')
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
            ->andReturn($nonNormalisedResult);

        $this->assertEquals(
            $normalisedResult,
            $this->sut->getScoreOrderedIdsBySectorInScope($stockId, $sectorsId)
        );
    }

    public function testGetSuccessfulDaCountInScope()
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
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('epa.licence', 'l')
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
            ->shouldReceive('andWhere')
            ->with('epa.inScope = 1')
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
            $this->sut->getSuccessfulDaCountInScope($stockId, $jurisdictionId)
        );
    }

    public function testGetUnsuccessfulScoreOrderedIdsInScopeWithoutTrafficAreaId()
    {
        $nonNormalisedResult = [
            ['id' => 35],
            ['id' => 37],
            ['id' => 41]
        ];

        $normalisedResult = [35, 37, 41];

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
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
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
            ->with('epa.inScope = 1')
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
            ->andReturn($nonNormalisedResult);

        $this->assertEquals(
            $normalisedResult,
            $this->sut->getUnsuccessfulScoreOrderedIdsInScope($stockId)
        );
    }

    public function testGetUnsuccessfulScoreOrderedIdsInScopeWithTrafficAreaId()
    {
        $nonNormalisedResult = [
            ['id' => 35],
            ['id' => 37],
            ['id' => 41]
        ];

        $normalisedResult = [35, 37, 41];

        $stockId = 3;
        $trafficAreaId = 12;

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
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
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
            ->with('epa.inScope = 1')
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
            ->shouldReceive('innerJoin')
            ->with('epa.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(l.trafficArea) = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $trafficAreaId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($nonNormalisedResult);

        $this->assertEquals(
            $normalisedResult,
            $this->sut->getUnsuccessfulScoreOrderedIdsInScope($stockId, $trafficAreaId)
        );
    }

    public function testGetSuccessfulCountInScope()
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
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
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
            ->with('epa.inScope = 1')
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
            $this->sut->getSuccessfulCountInScope($stockId)
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

    public function testFetchDeviationSourceValues()
    {
        $deviationSourceValues = [
            [
                'candidatePermitId' => 102,
                'licNo' => 'PD2737280',
                'applicationId' => 202,
                'permitsRequired' => 12
            ],
            [
                'candidatePermitId' => 104,
                'licNo' => 'OG4569803',
                'applicationId' => 205,
                'permitsRequired' => 6
            ]
        ];

        $stockId = 3;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('icp.id as candidatePermitId, l.licNo, epa.id as applicationId, epa.permitsRequired')
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
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('epa.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('epa.inScope = 1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($deviationSourceValues);

        $this->assertEquals(
            $deviationSourceValues,
            $this->sut->fetchDeviationSourceValues($stockId)
        );
    }

    public function testGetSuccessfulScoreOrderedInScope()
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
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.ecmtPermitApplication', 'epa')
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
            ->with('epa.inScope = 1')
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
            $this->sut->getSuccessfulScoreOrderedInScope($stockId)
        );
    }

    public function testFetchScoringReport()
    {
        $scoringReport = [
            'row1' => 'rowContent1',
            'row2' => 'rowContent2'
        ];

        $stockId = 3;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with(
                'icp.id as candidatePermitId, ' .
                'epa.id as applicationId, ' .
                'o.name as organisationName, ' .
                'icp.applicationScore as candidatePermitApplicationScore, ' .
                'icp.intensityOfUse as candidatePermitIntensityOfUse, ' .
                'icp.randomFactor as candidatePermitRandomFactor, ' .
                'icp.randomizedScore as candidatePermitRandomizedScore, ' .
                'IDENTITY(epa.internationalJourneys) as applicationInternationalJourneys, ' .
                's.name as applicationSectorName, ' .
                'l.licNo as licenceNo, ' .
                'ta.id as trafficAreaId, ' .
                'ta.name as trafficAreaName, ' .
                'icp.successful as candidatePermitSuccessful, ' .
                'IDENTITY(icp.irhpPermitRange) as candidatePermitRangeId'
            )
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
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('epa.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('epa.sectors', 's')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('l.trafficArea', 'ta')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('l.organisation', 'o')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('epa.inScope = 1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($scoringReport);

        $this->assertEquals(
            $scoringReport,
            $this->sut->fetchScoringReport($stockId)
        );
    }
}
