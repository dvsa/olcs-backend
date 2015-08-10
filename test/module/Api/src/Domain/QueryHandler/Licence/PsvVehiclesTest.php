<?php

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\PsvVehicles;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Licence\PsvVehicles as Qry;
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
        $this->mockRepo('Licence', Repository\Licence::class);
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

        /** @var Entity\Licence\Licence|m\MockInterface $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $licence->shouldReceive('hasPsvBreakdown')->andReturn(false);
        $licence->shouldReceive('getOtherActiveLicences->isEmpty')->andReturn(true);
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
            ->andReturn(7)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

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

        /** @var Entity\Licence\Licence|m\MockInterface $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(false);
        $licence->shouldReceive('hasPsvBreakdown')->andReturn(true);
        $licence->shouldReceive('getOtherActiveLicences->isEmpty')->andReturn(false);
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
            ->andReturn(7)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

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
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM)
            ->andReturn($mediumVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL, true)
            ->andReturn($smallVehicles)
            ->shouldReceive('getPsvVehiclesByType')
            ->with($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM, true)
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
            'canTransfer' => true,
            'hasBreakdown' => true
        ];

        $this->assertEquals($expected, $data);
    }
}
