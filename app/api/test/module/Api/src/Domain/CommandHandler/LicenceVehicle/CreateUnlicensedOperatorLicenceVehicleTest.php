<?php

/**
 * Create Unlicensed Operator Licence Vehicle Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceVehicle;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceVehicle\CreateUnlicensedOperatorLicenceVehicle as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as LicenceVehicleEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle as VehicleEntity;
use Dvsa\Olcs\Transfer\Command\LicenceVehicle\CreateUnlicensedOperatorLicenceVehicle as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Unlicensed Operator Licence Vehicle Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CreateUnlicensedOperatorLicenceVehicleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleCommandGoods()
    {
        $organisationId = 69;
        $licenceVehicleId = 99;
        $vehicleId = 999;

        $command = Cmd::create(
            [
                'organisation' => $organisationId,
                'vrm' => 'ABC1234',
                'platedWeight' => 895,
            ]
        );

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class);

        $this->repoMap['Organisation']
            ->shouldReceive('fetchById')
            ->with($organisationId)
            ->andReturn($organisation);

        $organisation
            ->shouldReceive('getLicences->first')
            ->andReturn($licence);

        $savedLicenceVehicle = null;

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('save')
            ->with(m::type(LicenceVehicleEntity::class))
            ->andReturnUsing(
                function (
                    LicenceVehicleEntity $licenceVehicle
                ) use (
                    &$savedLicenceVehicle,
                    $vehicleId,
                    $licenceVehicleId
                ) {
                    $savedLicenceVehicle = $licenceVehicle;
                    $this->assertEquals('ABC1234', $licenceVehicle->getVehicle()->getVrm());
                    $this->assertEquals(895, $licenceVehicle->getVehicle()->getPlatedWeight());
                    $licenceVehicle->setId($licenceVehicleId);
                    $licenceVehicle->getVehicle()->setId($vehicleId);
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'licenceVehicle' => $licenceVehicleId,
                'vehicle' => $vehicleId,
            ],
            $result->getIds()
        );

        $this->assertEquals(
            [
                'LicenceVehicle created',
                'Vehicle created',
            ],
            $result->getMessages()
        );
    }

    public function testHandleCommandPsv()
    {
        $organisationId = 69;
        $licenceVehicleId = 99;
        $vehicleId = 999;

        $command = Cmd::create(
            [
                'organisation' => $organisationId,
                'vrm' => 'ABC1234',
            ]
        );

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class);

        $this->repoMap['Organisation']
            ->shouldReceive('fetchById')
            ->with($organisationId)
            ->andReturn($organisation);

        $organisation
            ->shouldReceive('getLicences->first')
            ->andReturn($licence);

        $savedLicenceVehicle = null;

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('save')
            ->with(m::type(LicenceVehicleEntity::class))
            ->andReturnUsing(
                function (
                    LicenceVehicleEntity $licenceVehicle
                ) use (
                    &$savedLicenceVehicle,
                    $vehicleId,
                    $licenceVehicleId
                ) {
                    $savedLicenceVehicle = $licenceVehicle;
                    $this->assertEquals('ABC1234', $licenceVehicle->getVehicle()->getVrm());
                    $licenceVehicle->setId($licenceVehicleId);
                    $licenceVehicle->getVehicle()->setId($vehicleId);

                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'licenceVehicle' => $licenceVehicleId,
                'vehicle' => $vehicleId,
            ],
            $result->getIds()
        );

        $this->assertEquals(
            [
                'LicenceVehicle created',
                'Vehicle created',
            ],
            $result->getMessages()
        );
    }
}
