<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitJurisdictionQuota;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitJurisdictionQuota as IrhpPermitJurisdictionQuotaEntity;
use Mockery as m;

/**
 * IRHP Permit Jurisdiction Quota test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitJurisdictionQuotaTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitJurisdictionQuota::class);
    }

    public function testFetchByNonZeroQuota()
    {
        $expectedResult = [
            'jurisdictionId' => 8,
            'quotaNumber' => 320
        ];
        $stockId = 5;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('IDENTITY(ipjq.trafficArea) as jurisdictionId, ipjq.quotaNumber')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitJurisdictionQuotaEntity::class, 'ipjq')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('ipjq.quotaNumber > 0')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(ipjq.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchByNonZeroQuota($stockId)
        );
    }
}
