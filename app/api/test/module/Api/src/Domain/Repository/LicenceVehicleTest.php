<?php

/**
 * LicenceVehicle test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as LicenceVehicleEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Query\Application\GoodsVehicles as AppGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Variation\GoodsVehicles as VarGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\Licence\GoodsVehicles as LicGoodsVehicles;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Doctrine\ORM\QueryBuilder;

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

    public function testGetAllPsvVehiclesIncludeRemoved()
    {
        /** @var ApplicationEntity $entity */
        $entity = m::mock(ApplicationEntity::class)->makePartial();

        /** @var LicenceVehicleEntity $matched1 */
        $matched1 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched1->setSpecifiedDate(new DateTime());

        /** @var LicenceVehicleEntity $matched2 */
        $matched2 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched2->setApplication($entity);
        $matched2->setRemovalDate(new DateTime());

        /** @var LicenceVehicleEntity $unmatched1 */
        $unmatched1 = m::mock(LicenceVehicleEntity::class)->makePartial();

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($matched1);
        $licenceVehicles->add($matched2);
        $licenceVehicles->add($unmatched1);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceVehicles($licenceVehicles);

        $entity->setLicence($licence);

        /** @var ArrayCollection $collection */
        $collection = $this->sut->getAllPsvVehicles($entity, true);

        $this->assertTrue($collection->contains($matched1));
        $this->assertTrue($collection->contains($matched2));
        $this->assertFalse($collection->contains($unmatched1));
    }

    public function testGetAllPsvVehiclesDontIncludeRemoved()
    {
        /** @var ApplicationEntity $entity */
        $entity = m::mock(ApplicationEntity::class)->makePartial();

        /** @var LicenceVehicleEntity $matched1 */
        $matched1 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched1->setSpecifiedDate(new DateTime());

        /** @var LicenceVehicleEntity $unmatched1 */
        $unmatched1 = m::mock(LicenceVehicleEntity::class)->makePartial();

        /** @var LicenceVehicleEntity $unmatched2 */
        $unmatched2 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $unmatched2->setApplication($entity);
        $unmatched2->setRemovalDate(new DateTime());

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($matched1);
        $licenceVehicles->add($unmatched1);
        $licenceVehicles->add($unmatched2);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceVehicles($licenceVehicles);

        $entity->setLicence($licence);

        /** @var ArrayCollection $collection */
        $collection = $this->sut->getAllPsvVehicles($entity, false);

        $this->assertTrue($collection->contains($matched1));
        $this->assertFalse($collection->contains($unmatched1));
        $this->assertFalse($collection->contains($unmatched2));
    }

    public function testGetAllPsvVehiclesIncludeRemovedLicence()
    {
        /** @var LicenceEntity $entity */
        $entity = m::mock(LicenceEntity::class)->makePartial();

        /** @var LicenceVehicleEntity $matched1 */
        $matched1 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched1->setSpecifiedDate(new DateTime());

        /** @var LicenceVehicleEntity $matched2 */
        $matched2 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched2->setSpecifiedDate(new DateTime());
        $matched2->setRemovalDate(new DateTime());

        /** @var LicenceVehicleEntity $unmatched1 */
        $unmatched1 = m::mock(LicenceVehicleEntity::class)->makePartial();

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($matched1);
        $licenceVehicles->add($matched2);
        $licenceVehicles->add($unmatched1);

        $entity->setLicenceVehicles($licenceVehicles);

        /** @var ArrayCollection $collection */
        $collection = $this->sut->getAllPsvVehicles($entity, true);

        $this->assertTrue($collection->contains($matched1));
        $this->assertTrue($collection->contains($matched2));
        $this->assertFalse($collection->contains($unmatched1));
    }

    public function testGetPsvVehiclesByType()
    {
        $mockType1 = m::mock(RefData::class);
        $mockType2 = m::mock(RefData::class);

        /** @var LicenceEntity $entity */
        $entity = m::mock(LicenceEntity::class)->makePartial();

        /** @var Vehicle $vehicle1 */
        $vehicle1 = m::mock(Vehicle::class)->makePartial();
        $vehicle1->setPsvType($mockType1);

        /** @var Vehicle $vehicle2 */
        $vehicle2 = m::mock(Vehicle::class)->makePartial();
        $vehicle2->setPsvType($mockType2);

        /** @var LicenceVehicleEntity $matched1 */
        $matched1 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched1->setSpecifiedDate(new DateTime());
        $matched1->setVehicle($vehicle1);

        /** @var LicenceVehicleEntity $matched2 */
        $matched2 = m::mock(LicenceVehicleEntity::class)->makePartial();
        $matched2->setSpecifiedDate(new DateTime());
        $matched2->setRemovalDate(new DateTime());
        $matched2->setVehicle($vehicle2);

        /** @var LicenceVehicleEntity $unmatched1 */
        $unmatched1 = m::mock(LicenceVehicleEntity::class)->makePartial();

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($matched1);
        $licenceVehicles->add($matched2);
        $licenceVehicles->add($unmatched1);

        $entity->setLicenceVehicles($licenceVehicles);

        $this->em->shouldReceive('getReference')
            ->with(RefData::class, Vehicle::PSV_TYPE_SMALL)
            ->andReturn($mockType1);

        /** @var ArrayCollection $collection */
        $collection = $this->sut->getPsvVehiclesByType($entity, Vehicle::PSV_TYPE_SMALL, true);

        $this->assertTrue($collection->contains($matched1));
        $this->assertFalse($collection->contains($matched2));
        $this->assertFalse($collection->contains($unmatched1));
    }

    public function testFetchByVehicleId()
    {
        $mockQb = m::mock(QueryBuilder::class);
        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('m.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('m.vehicle', 'v')
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.vehicle', ':vehicle')->andReturn('condition');
        $mockQb->shouldReceive('where')->with('condition')->andReturnSelf();
        $mockQb->shouldReceive('orderBy')->with('m.specifiedDate', 'DESC')->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('vehicle', 1)->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');
        $this->assertSame('result', $this->sut->fetchByVehicleId(1));
    }

    public function testFetchDuplicates()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(111);

        $vrm = 'AB11ABC';

        $qb = $this->createMockQb('{{QUERY}}');
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getResult')
            ->once()
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchDuplicates($licence, $vrm));

        $this->assertEquals(
            '{{QUERY}} INNER JOIN m.vehicle v'
            . ' INNER JOIN m.licence l'
            . ' AND v.vrm = [[AB11ABC]]'
            . ' AND m.specifiedDate IS NOT NULL'
            . ' AND m.removalDate IS NULL'
            . ' AND l.id != [[111]]'
            . ' AND l.goodsOrPsv = [[lcat_gv]]'
            . ' AND l.status IN ["lsts_curtailed","lsts_valid","lsts_suspended"]'
            . ' AND m.warningLetterSeedDate IS NULL',
            $this->query
        );
    }

    public function testFetchQueuedForWarning()
    {
        $now = new DateTime();
        $expectedDate = $now->sub(new \DateInterval('P28D'))->format(\DateTime::W3C);

        $qb = $this->createMockQb('{{QUERY}}');
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getResult')
            ->once()
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchQueuedForWarning());

        $this->assertEquals(
            '{{QUERY}} INNER JOIN m.licence l'
            . ' AND l.status IN ["lsts_curtailed","lsts_valid","lsts_suspended"]'
            . ' AND m.warningLetterSeedDate < [['.$expectedDate.']]'
            . ' AND m.warningLetterSentDate IS NULL'
            . ' AND m.removalDate IS NULL',
            $this->query
        );
    }
}
