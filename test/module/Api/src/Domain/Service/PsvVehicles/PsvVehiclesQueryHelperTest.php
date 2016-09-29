<?php

/**
 * Psv Vehicles Query Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Service\PsvVehicles;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Licence\PsvVehicles as Qry;

/**
 * Psv Vehicles Query Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehiclesQueryHelperTest extends MockeryTestCase
{
    /**
     * @var PsvVehiclesQueryHelper
     */
    protected $sut;

    protected $licenceVehicleRepo;

    public function setUp()
    {
        $this->licenceVehicleRepo = m::mock(LicenceVehicle::class)->makePartial();

        /** @var ServiceManager $sm */
        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->setService('RepositoryServiceManager', $sm);
        $sm->setService('LicenceVehicle', $this->licenceVehicleRepo);

        $this->sut = new PsvVehiclesQueryHelper();
        $this->sut->createService($sm);
    }

    public function testGetCommonQueryFlagsLicence()
    {
        $query = Qry::create(
            [
                'id' => 111,
                'includeRemoved' => true
            ]
        );

        /* @var $licence Entity\Licence\Licence|m\MockInterface */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(111);
        $licence->setTotAuthVehicles(3);

        $vehicle1 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $vehicle1->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['VEHICLE1']);

        $vehicle2 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $vehicle2->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['VEHICLE2']);

        $vehicles = new ArrayCollection();
        $vehicles->add($vehicle1);
        $vehicles->add($vehicle2);

        $this->licenceVehicleRepo->shouldReceive('getAllPsvVehicles')->with($licence)->once()
            ->andReturn($vehicles)
            ->shouldReceive('getAllPsvVehicles')->with($licence, true)->once()
            ->andReturn($vehicles);

        $return = $this->sut->getCommonQueryFlags($licence, $query);

        $expected = [
            'vehicles' => [
                ['VEHICLE1'],
                ['VEHICLE2']
            ],
            'total' => 2
        ];

        $this->assertEquals($expected, $return);
    }

    public function testGetCommonQueryFlagsApplication()
    {
        $query = Qry::create(
            [
                'id' => 111,
                'includeRemoved' => false
            ]
        );

        /* @var $application Entity\Application\Application|m\MockInterface */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setId(111);
        $application->setTotAuthVehicles(10);

        $vehicle1 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $vehicle1->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['VEHICLE1']);

        $vehicle2 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $vehicle2->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['VEHICLE2']);

        $vehicles = new ArrayCollection();
        $vehicles->add($vehicle1);
        $vehicles->add($vehicle2);

        $this->licenceVehicleRepo->shouldReceive('getAllPsvVehicles')->with($application)->once()
            ->andReturn($vehicles)
            ->shouldReceive('getAllPsvVehicles')->with($application, false)->once()
            ->andReturn($vehicles);

        $return = $this->sut->getCommonQueryFlags($application, $query);

        $expected = [
            'vehicles' => [
                ['VEHICLE1'],
                ['VEHICLE2']
            ],
            'total' => 2
        ];

        $this->assertEquals($expected, $return);
    }
}
