<?php

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as Cmd;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGoodsVehicle extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Vehicle', 'LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $this->checkIfVrmAlreadyExistsOnLicence($licence, $command->getVrm());

        // If we haven't confirmed, then we need to check if we need to confirm
        if ($command->getConfirm() !== true) {
            $this->checkForConfirmation($licence, $command);
        }

        $vehicle = new Vehicle();
        $vehicle->setVrm($command->getVrm());
        $vehicle->setPlatedWeight($command->getPlatedWeight());

        $this->getRepo('Vehicle')->save($vehicle);

        $result->addId('vehicle', $vehicle->getId());

        $licenceVehicle = new LicenceVehicle($licence, $vehicle);
        if ($command->getSpecifiedDate() !== null) {
            $licenceVehicle->setSpecifiedDate(new \DateTime($command->getSpecifiedDate()));
        }
        // @todo implement licenceVehicle fields

        $this->getRepo('LicenceVehicle')->save($licenceVehicle);

        $result->addId('licenceVehicle', $licenceVehicle->getId());

        return $result;
    }

    /**
     * Check if the vrm exists on any other licences
     */
    protected function checkForConfirmation(LicenceEntity $licence, Cmd $command)
    {
        $licences = $this->getRepo('Vehicle')->fetchLicencesForVrm($command->getVrm());

        if (empty($licences)) {
            return;
        }

        $otherLicences = [];

        foreach ($licences as $otherLicence) {
            if ($otherLicence !== $licence) {
                $otherLicences[] = $otherLicence;
            }
        }

        if (empty($otherLicences)) {
            return;
        }

        if ($this->isGranted(Permission::INTERNAL_USER)) {

            $refs = [];

            /** @var LicenceEntity $otherLicence */
            foreach ($otherLicences as $otherLicence) {
                $licNo = $otherLicence->getLicNo();

                if (empty($licNo)) {
                    $applications = $otherLicence->getApplications();
                    if ($applications->count() > 1) {
                        $licNo = 'APP-' . $applications->first()->getId();
                    }
                }

                $refs[] = $licNo;
            }

            $message = json_encode($refs);
        } else {
            $message = 'Vehicle exists on other licence';
        }

        throw new RequiresConfirmationException($message, Vehicle::ERROR_VRM_OTHER_LICENCE);
    }

    /**
     * Check whether the VRM already exist on this licence
     */
    protected function checkIfVrmAlreadyExistsOnLicence(LicenceEntity $licence, $vrm)
    {
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->isNull('removalDate'));

        $currentLicenceVehicles = $licence->getLicenceVehicles()->matching($criteria);

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
