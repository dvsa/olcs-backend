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
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Query\Fee\FeeTypeList as FeeTypeListQry;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;

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

        $qb->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                    ->shouldReceive('execute')
                    ->andReturn(['RESULTS'])
                    ->getMock()
            );

        $this->assertEquals('RESULTS', $this->sut->fetchLatestForOverpayment());

        $expectedQuery = 'QUERY AND ft.feeType = [[ADJUSTMENT]] ORDER BY ft.effectiveFrom DESC LIMIT 1';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForApplication()
    {
        $applicationId = 99;

        $mockApplication = m::mock(ApplicationEntity::class)
            ->shouldReceive('getId')
            ->andReturn($applicationId)
            ->shouldReceive('getLicenceType')
            ->andReturn(new RefData('LICENCE_TYPE'))
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn('GOODS_OR_PSV')
            ->getMock();

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

        $this->em
            ->shouldReceive('getReference')->with(ApplicationEntity::class, $applicationId)
            ->andReturn($mockApplication);

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
            . ' AND ft.effectiveFrom <= [[2014-10-26T00:00:00+00:00]]'
            . ' AND ft.isMiscellaneous = [[0]]'
            . ' AND ft.feeType IN ["APP","VAR","GRANT","GRANTINT"]'
            . ' AND ft.goodsOrPsv = [[GOODS_OR_PSV]]'
            . ' AND ft.licenceType = [[LICENCE_TYPE]]'
            . ' ORDER BY ftft.id ASC'
            . ' ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLicence()
    {
        $licenceId = 99;

        $mockLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getId')
            ->andReturn($licenceId)
            ->shouldReceive('getLicenceType')
            ->andReturn(new RefData('LICENCE_TYPE'))
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn('GOODS_OR_PSV')
            ->getMock();

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

        $this->em
            ->shouldReceive('getReference')->with(LicenceEntity::class, $licenceId)
            ->andReturn($mockLicence);

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
            . ' AND ft.effectiveFrom <= [[2014-10-26T00:00:00+00:00]]'
            . ' AND ft.feeType IN ["CONT"]'
            . ' AND ft.licenceType = [[LICENCE_TYPE]]'
            . ' ORDER BY ftft.id ASC'
            . ' ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForOrganisation()
    {
        $now = new DateTime();
        $expectedDate = $now->format(DateTime::W3C);

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

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'organisation' => $organisationId,
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
            . ' AND ft.effectiveFrom <= [['.$expectedDate.']]'
            . ' AND ft.isMiscellaneous = [[0]]'
            . ' AND ft.feeType IN ["IRFOGVPERMIT","IRFOPSVANN","IRFOPSVAPP","IRFOPSVCOPY"]'
            . ' ORDER BY ftft.id ASC'
            . ' ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForBusReg()
    {
        $now = new DateTime();
        $expectedDate = $now->format(DateTime::W3C);

        $mockLicence = m::mock(LicenceEntity::class);

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

        $this->em
            ->shouldReceive('getReference')->with(LicenceEntity::class, 99)
            ->andReturn($mockLicence);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'busReg' => 1412,
                'licence' => 99,
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
            . ' AND ft.effectiveFrom <= [['.$expectedDate.']]'
            . ' AND ft.isMiscellaneous = [[0]]'
            . ' AND ft.feeType IN ["BUSAPP","BUSVAR"]'
            . ' ORDER BY ftft.id ASC'
            . ' ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListMiscellaneous()
    {
        $now = new DateTime();
        $expectedDate = $now->format(DateTime::W3C);

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

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['RESULTS']);

        $queryDto = FeeTypeListQry::create(
            [
                'isMiscellaneous' => 'Y',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expectedQuery = 'QUERY'
            . ' AND ft.effectiveFrom <= [['.$expectedDate.']]'
            . ' AND ft.isMiscellaneous = [[1]]'
            . ' ORDER BY ftft.id ASC'
            . ' ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testGetLatestIrfoFeeTypeForGvPermits()
    {
        $organisation = new Organisation();
        $irfoGvPermitType = new IrfoGvPermitType();
        $feeTypeFeeType = new RefData('feeTypefeeType');
        $irfoGvPermitType->setIrfoFeeType($feeTypeFeeType);

        $status = new RefData('status');

        $irfoEntity = new IrfoGvPermit($organisation, $irfoGvPermitType, $status);

        $this->sut->shouldReceive('fetchLatestForIrfo')->andReturn(['foo']);

        $this->assertEquals($this->sut->getLatestIrfoFeeType($irfoEntity, $feeTypeFeeType), ['foo']);
    }

    public function testGetLatestIrfoFeeTypeForPsvAuth()
    {
        $organisation = new Organisation();
        $irfoPsvAuthType = new IrfoPsvAuthType();
        $feeTypeFeeType = new RefData('feeTypefeeType');
        $irfoPsvAuthType->setIrfoFeeType($feeTypeFeeType);

        $status = new RefData('status');

        $irfoEntity = new IrfoPsvAuth($organisation, $irfoPsvAuthType, $status);

        $this->sut->shouldReceive('fetchLatestForIrfo')->andReturn(['foo']);

        $this->assertEquals($this->sut->getLatestIrfoFeeType($irfoEntity, $feeTypeFeeType), ['foo']);
    }

    public function testGetLatestIrfoFeeTypeForUnknownEntity()
    {
        $this->expectException(NotFoundException::class);

        $feeTypeFeeType = new RefData('feeTypefeeType');

        // force exception by passing something other than IrfoGvPermit or IrfoPsvAuth Entities
        $irfoEntity = new \StdClass();

        $this->sut->getLatestIrfoFeeType($irfoEntity, $feeTypeFeeType);
    }
}
