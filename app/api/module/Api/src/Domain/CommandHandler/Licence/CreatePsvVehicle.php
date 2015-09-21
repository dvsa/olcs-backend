<?php

/**
 * Create Psv Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreatePsvVehicle as Cmd;

/**
 * Create Psv Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreatePsvVehicle extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Vehicle', 'LicenceVehicle'];

    private $typeMap = [
        'small' => Vehicle::PSV_TYPE_SMALL,
        'medium' => Vehicle::PSV_TYPE_MEDIUM,
        'large' => Vehicle::PSV_TYPE_LARGE
    ];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $this->checkIfVrmAlreadyExistsOnLicence($licence, $command->getVrm());

        $existedVehicle = $this->getRepo('Vehicle')->fetchByVrm($command->getVrm());
        if (count($existedVehicle)) {
            $vehicle = $existedVehicle[0];
        } else {
            $vehicle = new Vehicle();
            $vehicle->setVrm($command->getVrm());
            $vehicle->setMakeModel($command->getMakeModel());
            $vehicle->setIsNovelty($command->getIsNovelty());
            $vehicle->setPsvType($this->getRepo()->getRefdataReference($this->typeMap[$command->getType()]));
            $this->getRepo('Vehicle')->save($vehicle);
        }

        $this->result->addMessage('Vehicle created');
        $this->result->addId('vehicle', $vehicle->getId());

        $licenceVehicle = new LicenceVehicle($licence, $vehicle);

        if ($this->isGranted(Permission::INTERNAL_USER)) {
            $licenceVehicle->setReceivedDate(new DateTime($command->getReceivedDate()));
            $licenceVehicle->setSpecifiedDate(new DateTime($command->getSpecifiedDate()));
        } else {
            $licenceVehicle->setSpecifiedDate(new DateTime());
        }

        $this->getRepo('LicenceVehicle')->save($licenceVehicle);

        $this->result->addMessage('Licence Vehicle created');
        $this->result->addId('licenceVehicle', $licenceVehicle->getId());

        return $this->result;
    }

    /**
     * Check whether the VRM already exist on this licence
     */
    protected function checkIfVrmAlreadyExistsOnLicence(LicenceEntity $licence, $vrm)
    {
        $currentLicenceVehicles = $licence->getActiveVehicles(false);

        if ($currentLicenceVehicles->count() < 1) {
            return;
        }

        foreach ($currentLicenceVehicles as $licenceVehicle) {
            if ($licenceVehicle->getVehicle()->getVrm() == $vrm) {
                throw new ValidationException(
                    [
                        'vrm' => [
                            Vehicle::ERROR_VRM_EXISTS => 'Vehicle already exists on this licence'
                        ]
                    ]
                );
            }
        }
    }
}
