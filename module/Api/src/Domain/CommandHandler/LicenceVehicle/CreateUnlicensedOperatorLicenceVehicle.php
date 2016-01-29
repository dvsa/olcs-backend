<?php

/**
 * Create Unlicensed Operator Licence Vehicle
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\LicenceVehicle\CreateUnlicensedOperatorLicenceVehicle as Cmd;

/**
 * Create Unlicensed Operator Licence Vehicle
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateUnlicensedOperatorLicenceVehicle extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceVehicle';

    protected $extraRepos = ['Organisation'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $organisation = $this->getRepo('Organisation')->fetchById($command->getOrganisation());

        $licence = $organisation->getLicences()->first();

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = $this->createLicenceVehicleObject($command, $licence);

        $this->getRepo()->save($licenceVehicle);

        $this->result
            ->addId('licenceVehicle', $licenceVehicle->getId())
            ->addMessage('LicenceVehicle created')
            ->addId('vehicle', $licenceVehicle->getVehicle()->getId())
            ->addMessage('Vehicle created');

        return $this->result;
    }

    /**
     * @param Cmd $command
     * @param Licence $licence
     * @return LicenceVehicle
     */
    private function createLicenceVehicleObject($command, $licence)
    {
        $vehicle = new Vehicle();
        $vehicle->setVrm($command->getVrm());

        if (!is_null($command->getPlatedWeight())) {
            $vehicle->setPlatedWeight($command->getPlatedWeight());
        }

        return new LicenceVehicle($licence, $vehicle);
    }
}
