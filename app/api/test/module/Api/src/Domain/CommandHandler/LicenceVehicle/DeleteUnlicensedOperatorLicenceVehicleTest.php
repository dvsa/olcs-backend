<?php

/**
 * Delete Unlicensed Operator Licence Vehicle Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceVehicle;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceVehicle\DeleteUnlicensedOperatorLicenceVehicle as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Api\Domain\Repository\Vehicle as VehicleRepo;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as LicenceVehicleEntity;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle as VehicleEntity;
use Dvsa\Olcs\Transfer\Command\LicenceVehicle\DeleteUnlicensedOperatorLicenceVehicle as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Delete Unlicensed Operator Licence Vehicle Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DeleteUnlicensedOperatorLicenceVehicleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);
        $this->mockRepo('Vehicle', VehicleRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 69;
        $vehicleId = 99;

        $command = Cmd::create(
            [
                'id' => $id,
            ]
        );

        $licenceVehicle = m::mock(LicenceVehicleEntity::class);
        $vehicle = m::mock(VehicleEntity::class);

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licenceVehicle);

        $licenceVehicle
            ->shouldReceive('getId')
            ->once()
            ->andReturn($id);
        $licenceVehicle
            ->shouldReceive('getVehicle')
            ->once()
            ->andReturn($vehicle);
        $vehicle
            ->shouldReceive('getId')
            ->andReturn($vehicleId);

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('delete')
            ->with($licenceVehicle)
            ->once();

        $this->repoMap['Vehicle']
            ->shouldReceive('delete')
            ->with($vehicle)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'licenceVehicle' => $id,
                'vehicle' => $vehicleId,
            ],
            $result->getIds()
        );

        $this->assertEquals(
            [
                'LicenceVehicle deleted',
                'Vehicle deleted',
            ],
            $result->getMessages()
        );
    }
}
