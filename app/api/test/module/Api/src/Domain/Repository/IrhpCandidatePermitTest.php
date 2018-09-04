<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Mockery as m;

/**
 * IRHP Candidate Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermitTest extends RepositoryTestCase
{
    /** @var QueryBuilder */
    protected $queryBuilder;

    public function setUp()
    {
        $this->setUpSut(IrhpCandidatePermit::class);

        $this->queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($this->queryBuilder);
    }

    public function testGetCountLackingRandomisedScore()
    {
        $candidatePermitCount = 35;
        $stockId = 5;

        $this->queryBuilder->shouldReceive('select')
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
            ->with('ipa.status = \'ecmt_permit_uc\'')
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

        $this->queryBuilder->shouldReceive('select')
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
            ->with('ipa.status = \'ecmt_permit_uc\'')
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

        $this->queryBuilder->shouldReceive('select')
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

        $this->queryBuilder->shouldReceive('select')
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
            ->with('ipa.status = \'ecmt_permit_uc\'')
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

        $this->queryBuilder->shouldReceive('select')
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

        $query = m::mock(AbstractQuery::class);
        $query->shouldReceive('execute')
            ->withNoArgs()
            ->once();

        $this->queryBuilder->shouldReceive('update')
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
}
