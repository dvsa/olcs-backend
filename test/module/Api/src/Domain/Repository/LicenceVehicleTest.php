<?php

/**
 * LicenceVehicle test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Application\GoodsVehicles as AppGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Variation\GoodsVehicles as VarGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Licence\GoodsVehicles as LicGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;

/**
 * LicenceVehicle test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicleTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(LicenceVehicleRepo::class);
    }

    public function testCreatePaginatedVehiclesDataForApplicationQuery()
    {
        $data = [
            'disc' => 'Y',
            'vrm' => 'A',
            'includeRemoved' => false,
            'specified' => 'Y',
            'page' => 3,
            'limit' => 10
        ];
        $qry = AppGoodsVehicles::create($data);
        $appId = 111;
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForApplicationQuery($qry, $appId, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = Y
            . ' INNER JOIN m.goodsDiscs gd AND gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL'
            // VRM ~ A
            . ' AND v.vrm LIKE [[A%]]'
            // Include Removed = false
            . ' AND m.removalDate IS NULL'
            // Specified = Y
            . ' AND m.specifiedDate IS NOT NULL'
            . ' AND (m.application = [[111]] OR m.licence = [[222]])';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testCreatePaginatedVehiclesDataForApplicationQueryAlternative()
    {
        $data = [
            'disc' => 'N',
            'includeRemoved' => true,
            'specified' => 'N',
            'page' => 3,
            'limit' => 10
        ];
        $qry = AppGoodsVehicles::create($data);
        $appId = 111;
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForApplicationQuery($qry, $appId, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = N
            . ' LEFT JOIN m.goodsDiscs gd WITH gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL AND gd.id IS NULL'
            // VRM NULL
            // Include Removed = true
            // Specified = N
            . ' AND m.specifiedDate IS NULL'
            . ' AND m.application = [[111]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testCreatePaginatedVehiclesDataForVariationQuery()
    {
        $data = [
            'disc' => 'Y',
            'vrm' => 'A',
            'includeRemoved' => false,
            'specified' => 'Y',
            'page' => 3,
            'limit' => 10
        ];
        $qry = VarGoodsVehicles::create($data);
        $appId = 111;
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForVariationQuery($qry, $appId, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = Y
            . ' INNER JOIN m.goodsDiscs gd AND gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL'
            // VRM ~ A
            . ' AND v.vrm LIKE [[A%]]'
            // Include Removed = false
            . ' AND m.removalDate IS NULL'
            // Specified = Y
            . ' AND m.specifiedDate IS NOT NULL'
            . ' AND (m.application = [[111]] OR m.licence = [[222]])';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testCreatePaginatedVehiclesDataForVariationQueryAlternative()
    {
        $data = [
            'disc' => 'N',
            'includeRemoved' => true,
            'specified' => 'N',
            'page' => 3,
            'limit' => 10
        ];
        $qry = VarGoodsVehicles::create($data);
        $appId = 111;
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForVariationQuery($qry, $appId, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = N
            . ' LEFT JOIN m.goodsDiscs gd WITH gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL AND gd.id IS NULL'
            // VRM NULL
            // Include Removed = true
            // Specified = N
            . ' AND m.specifiedDate IS NULL'
            . ' AND (m.application = [[111]] OR m.licence = [[222]])';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testCreatePaginatedVehiclesDataForLicenceQuery()
    {
        $data = [
            'disc' => 'Y',
            'vrm' => 'A',
            'includeRemoved' => false,
            'specified' => 'Y',
            'page' => 3,
            'limit' => 10
        ];
        $qry = LicGoodsVehicles::create($data);
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForLicenceQuery($qry, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = Y
            . ' INNER JOIN m.goodsDiscs gd AND gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL'
            // VRM ~ A
            . ' AND v.vrm LIKE [[A%]]'
            // Include Removed = false
            . ' AND m.removalDate IS NULL'
            // Specified = Y
            . ' AND m.specifiedDate IS NOT NULL'
            . ' AND m.licence = [[222]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testCreatePaginatedVehiclesDataForLicenceQueryAlternative()
    {
        $data = [
            'disc' => 'N',
            'includeRemoved' => true,
            'specified' => 'N',
            'page' => 3,
            'limit' => 10
        ];
        $qry = LicGoodsVehicles::create($data);
        $licId = 222;

        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->shouldReceive('paginate')
            ->with(3, 10);

        $this->assertSame($qb, $this->sut->createPaginatedVehiclesDataForLicenceQuery($qry, $licId));

        $expectedQuery = '[QUERY] INNER JOIN m.vehicle v LEFT JOIN m.interimApplication in'
            // Disc = N
            . ' LEFT JOIN m.goodsDiscs gd WITH gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL AND gd.id IS NULL'
            // VRM NULL
            // Include Removed = true
            // Specified = N
            . ' AND m.specifiedDate IS NOT NULL'
            . ' AND m.licence = [[222]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
