<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
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
                'IDENTITY(icp.requestedEmissionsCategory) as candidatePermitRequestedEmissionsCategory, ' .
                'IDENTITY(icp.assignedEmissionsCategory) as candidatePermitAssignedEmissionsCategory, ' .
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
            ->with('epa.status = ?2')
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
            ->with(2, EcmtPermitApplication::STATUS_UNDER_CONSIDERATION)
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
