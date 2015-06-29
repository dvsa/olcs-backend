<?php

/**
 * GoodsVehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\GoodsVehicles;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Transfer\Query\Application\GoodsVehicles as Qry;
use ZfcRbac\Service\AuthorizationService;

/**
 * GoodsVehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GoodsVehicles();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $qb = m::mock(QueryBuilder::class);

        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->shouldReceive('serialize')
            ->with(['vehicle', 'goodsDiscs', 'interimApplication'])
            ->andReturn(['foo' => 'bar']);

        $licenceVehicles = [
            $licenceVehicle
        ];

        // Internal user
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        // Status allows reprint
        $status = m::mock(RefData::class)->makePartial();
        $status->setId(ApplicationEntity::APPLICATION_STATUS_GRANTED);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);
        $licence->shouldReceive('getActiveVehiclesCount')
            ->andReturn(2);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setStatus($status);
        $application->setLicence($licence);
        $application->shouldReceive('getRemainingSpaces')
            ->andReturn(3);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->repoMap['LicenceVehicle']->shouldReceive('createPaginatedVehiclesDataForApplicationQuery')
            ->with($query, 111, 222)
            ->andReturn($qb)
            ->shouldReceive('fetchPaginatedList')
            ->with($qb, Query::HYDRATE_OBJECT)
            ->andReturn($licenceVehicles)
            ->shouldReceive('fetchPaginatedCount')
            ->with($qb)
            ->andReturn(10);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $application->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $expected = [
            'foo' => 'bar',
            'canReprint' => true,
            'canTransfer' => false,
            'canExport' => false,
            'canPrintVehicle' => true,
            'licenceVehicles' => [
                'results' => [
                    ['foo' => 'bar']
                ],
                'count' => 10
            ],
            'spacesRemaining' => 3,
            'activeVehicleCount' => 2
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
