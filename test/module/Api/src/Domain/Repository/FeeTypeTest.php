<?php

/**
 * Fee Type repository test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as Repo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Query\Fee\FeeTypeList as FeeTypeListQry;
use Mockery as m;

/**
 * Fee Type repository test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTypeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class, true);
    }

    public function testFetchLatestForOverpayment()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('setMaxResults')
            ->with(1)
            ->andReturnSelf();

        $qb->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                    ->shouldReceive('execute')
                    ->andReturn(['RESULTS'])
                    ->getMock()
            );

        $this->assertEquals('RESULTS', $this->sut->fetchLatestForOverpayment());

        $expectedQuery = 'QUERY AND ft.feeType = [[ADJUSTMENT]] ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForApplication()
    {
        $applicationId = 99;

        $mockApplication = m::mock(ApplicationEntity::class)
            ->shouldReceive('getId')
            ->andReturn($applicationId)
            ->shouldReceive('getLicenceType')
            ->andReturn('LICENCE_TYPE')
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn('GOODS_OR_PSV')
            ->getMock();
        $mockTa = m::mock(TrafficAreaEntity::class)
            ->shouldReceive('getIsNi')
            ->andReturn(false)
            ->getMock();
        $mockApplication
            ->shouldReceive('getLicence->getTrafficArea')
            ->andReturn($mockTa);

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('feeType', 'ftft')
            ->once()
            ->andReturnSelf();

        $qb
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ftft.id', 'ASC')
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ft.effectiveFrom', 'DESC');

        $this->em
            ->shouldReceive('getReference')
            ->with(ApplicationEntity::class, $applicationId)
            ->andReturn($mockApplication);

        $this->em
            ->shouldReceive('getReference')
            ->with(TrafficAreaEntity::class, 'N')
            ->andReturn('NI_TRAFFIC_AREA');

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'application' => $applicationId,
                'effectiveDate' => '2014-10-26',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
         . ' AND ft.effectiveFrom <= [[2014-10-26T00:00:00+01:00]]'
         . ' AND ft.isMiscellaneous = [[0]]'
         . ' AND ft.costCentreRef != [[IR]]'
         . ' AND ft.goodsOrPsv = [[GOODS_OR_PSV]]'
         . ' AND ft.licenceType = [[LICENCE_TYPE]]'
         . ' AND (ft.trafficArea != [[NI_TRAFFIC_AREA]] OR ft.trafficArea IS NULL)';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLicence()
    {
        $licenceId = 99;

        $mockLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getId')
            ->andReturn($licenceId)
            ->shouldReceive('getLicenceType')
            ->andReturn('LICENCE_TYPE')
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn('GOODS_OR_PSV')
            ->getMock();
        $mockTa = m::mock(TrafficAreaEntity::class)
            ->shouldReceive('getIsNi')
            ->andReturn(false)
            ->getMock();
        $mockLicence
            ->shouldReceive('getTrafficArea')
            ->andReturn($mockTa);

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('feeType', 'ftft')
            ->once()
            ->andReturnSelf();

        $qb
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ftft.id', 'ASC')
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ft.effectiveFrom', 'DESC');

        $this->em
            ->shouldReceive('getReference')
            ->with(LicenceEntity::class, $licenceId)
            ->andReturn($mockLicence);

        $this->em
            ->shouldReceive('getReference')
            ->with(TrafficAreaEntity::class, 'N')
            ->andReturn('NI_TRAFFIC_AREA');

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'licence' => $licenceId,
                'effectiveDate' => '2014-10-26',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
         . ' AND ft.effectiveFrom <= [[2014-10-26T00:00:00+01:00]]'
         . ' AND ft.isMiscellaneous = [[0]]'
         . ' AND ft.costCentreRef != [[IR]]'
         . ' AND ft.goodsOrPsv = [[GOODS_OR_PSV]]'
         . ' AND ft.licenceType = [[LICENCE_TYPE]]'
         . ' AND (ft.trafficArea != [[NI_TRAFFIC_AREA]] OR ft.trafficArea IS NULL)';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForOrganisation()
    {
        $organisationId = 99;

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('feeType', 'ftft')
            ->once()
            ->andReturnSelf();

        $qb
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ftft.id', 'ASC')
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('ft.effectiveFrom', 'DESC');

        $this->em
            ->shouldReceive('getReference')
            ->with(TrafficAreaEntity::class, 'N')
            ->andReturn('NI_TRAFFIC_AREA');

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'organisation' => $organisationId,
                'effectiveDate' => '2014-10-26',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
         . ' AND ft.effectiveFrom <= [[2014-10-26T00:00:00+01:00]]'
         . ' AND ft.isMiscellaneous = [[0]]'
         . ' AND ft.costCentreRef = [[IR]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
