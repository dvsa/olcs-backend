<?php

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\PsvVehicles;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Application\PsvVehicles as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehiclesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PsvVehicles();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'id' => 111
            ]
        );

        /** @var Entity\Application\Application|m\MockInterface $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $application->shouldReceive('shouldShowSmallTable')
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
            ->andReturn(7)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

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

        $this->repoMap['LicenceVehicle']->shouldReceive('getPsvVehiclesByType')
            ->with($application, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($application, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM)
            ->andReturn($mediumVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($application, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE)
            ->andReturn($largeVehicles);

        $result = $this->sut->handleQuery($query);

        $data = $result->serialize();

        $expected = [
            'foo' => 'bar',
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
            'canTransfer' => false,
            'hasBreakdown' => false
        ];

        $this->assertEquals($expected, $data);
    }

    public function testHandleQueryWithoutLargeVehicles()
    {
        $query = Qry::create(
            [
                'id' => 111
            ]
        );

        /** @var Entity\Application\Application|m\MockInterface $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(false);
        $application->shouldReceive('shouldShowSmallTable')
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
            ->andReturn(7)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

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

        $this->repoMap['LicenceVehicle']->shouldReceive('getPsvVehiclesByType')
            ->with($application, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($application, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM)
            ->andReturn($mediumVehicles);

        $result = $this->sut->handleQuery($query);

        $data = $result->serialize();

        $expected = [
            'foo' => 'bar',
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
            'canTransfer' => false,
            'hasBreakdown' => false
        ];

        $this->assertEquals($expected, $data);
    }
}
