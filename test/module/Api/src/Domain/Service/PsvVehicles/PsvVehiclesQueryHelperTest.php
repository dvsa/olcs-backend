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

    public function testGetCommonQueryFlags()
    {
        $query = Qry::create(
            [
                'id' => 111,
                'includeRemoved' => true
            ]
        );

        /** @var Entity\Licence\Licence|m\MockInterface $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $licence->shouldReceive('shouldShowSmallTable')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('shouldShowMediumTable')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('shouldShowLargeTable')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('isSmallAuthExceeded')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('isMediumAuthExceeded')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('isLargeAuthExceeded')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('getAvailableSmallSpaces')
            ->with(1)
            ->andReturn(9)
            ->shouldReceive('getAvailableMediumSpaces')
            ->with(1)
            ->andReturn(8)
            ->shouldReceive('getAvailableLargeSpaces')
            ->with(1)
            ->andReturn(7);

        $smallVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $smallVehicle->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['type' => 'small']);

        $mediumVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $mediumVehicle->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['type' => 'medium']);

        $largeVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $largeVehicle->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['type' => 'large']);

        $smallVehicles = new ArrayCollection();
        $smallVehicles->add($smallVehicle);

        $mediumVehicles = new ArrayCollection();
        $mediumVehicles->add($mediumVehicle);

        $largeVehicles = new ArrayCollection();
        $largeVehicles->add($largeVehicle);

        $this->licenceVehicleRepo->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM)
            ->andReturn($mediumVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE)
            ->andReturn($largeVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL, true)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM, true)
            ->andReturn($mediumVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE, true)
            ->andReturn($largeVehicles);

        $return = $this->sut->getCommonQueryFlags($licence, $query);

        $expected = [
            'showSmallTable' => true,
            'showMediumTable' => true,
            'showLargeTable' => true,
            'smallAuthExceeded' => true,
            'mediumAuthExceeded' => true,
            'largeAuthExceeded' => true,
            'availableSmallSpaces' => 9,
            'availableMediumSpaces' => 8,
            'availableLargeSpaces' => 7,
            'small' => [
                ['type' => 'small']
            ],
            'medium' => [
                ['type' => 'medium']
            ],
            'large' => [
                ['type' => 'large']
            ],
            'total' => 3
        ];

        $this->assertEquals($expected, $return);
    }

    public function testHandleQueryWithoutLargeVehicles()
    {
        $query = Qry::create(
            [
                'id' => 111,
                'includeRemoved' => false
            ]
        );

        /** @var Entity\Licence\Licence|m\MockInterface $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(false);
        $licence->shouldReceive('shouldShowSmallTable')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('shouldShowMediumTable')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('shouldShowLargeTable')
            ->with(0)
            ->andReturn(true)
            ->shouldReceive('isSmallAuthExceeded')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('isMediumAuthExceeded')
            ->with(1)
            ->andReturn(true)
            ->shouldReceive('isLargeAuthExceeded')
            ->with(0)
            ->andReturn(true)
            ->shouldReceive('getAvailableSmallSpaces')
            ->with(1)
            ->andReturn(9)
            ->shouldReceive('getAvailableMediumSpaces')
            ->with(1)
            ->andReturn(8)
            ->shouldReceive('getAvailableLargeSpaces')
            ->with(0)
            ->andReturn(7);

        $smallVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $smallVehicle->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['type' => 'small']);

        $mediumVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $mediumVehicle->shouldReceive('serialize')
            ->with(['vehicle'])
            ->andReturn(['type' => 'medium']);

        $smallVehicles = new ArrayCollection();
        $smallVehicles->add($smallVehicle);

        $mediumVehicles = new ArrayCollection();
        $mediumVehicles->add($mediumVehicle);

        $this->licenceVehicleRepo->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM)
            ->andReturn($mediumVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL, false)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM, false)
            ->andReturn($mediumVehicles);

        $return = $this->sut->getCommonQueryFlags($licence, $query);

        $expected = [
            'showSmallTable' => true,
            'showMediumTable' => true,
            'showLargeTable' => true,
            'smallAuthExceeded' => true,
            'mediumAuthExceeded' => true,
            'largeAuthExceeded' => true,
            'availableSmallSpaces' => 9,
            'availableMediumSpaces' => 8,
            'availableLargeSpaces' => 7,
            'small' => [
                ['type' => 'small']
            ],
            'medium' => [
                ['type' => 'medium']
            ],
            'large' => [],
            'total' => 2
        ];

        $this->assertEquals($expected, $return);
    }
}
