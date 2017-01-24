<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

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

    /**
     * @param Cmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $this->checkIfVrmIsSection26($command->getVrm());
        $this->checkIfVrmAlreadyExistsOnLicence($licence, $command->getVrm());

        $duplicates = [];

        if ($command->getIdentifyDuplicates() === true) {
            $duplicates = $this->getRepo('LicenceVehicle')->fetchDuplicates($licence, $command->getVrm(), false);
        }

        // If we haven't confirmed, then we need to check if we need to confirm
        if ($command->getConfirm() !== true) {

            if ($command->getIdentifyDuplicates() === true) {
                $this->identifyDuplicates($duplicates);
            } else {
                $this->checkForConfirmation($licence, $command);
            }
        }

        if ($command->getIdentifyDuplicates() === true) {
            /** @var LicenceVehicle $duplicate */
            foreach ($duplicates as $duplicate) {
                $duplicate->updateDuplicateMark();
                $this->getRepo('LicenceVehicle')->save($duplicate);
            }
        }

        $existedVehicle = $this->getRepo('Vehicle')->fetchByVrm($command->getVrm());
        if (count($existedVehicle)) {
            $vehicle = $existedVehicle[0];
        } else {
            $vehicle = new Vehicle();
            $vehicle->setVrm($command->getVrm());
        }
        $vehicle->setPlatedWeight($command->getPlatedWeight());
        $this->getRepo('Vehicle')->save($vehicle);

        $result->addId('vehicle', $vehicle->getId());
        $result->addMessage('Vehicle created');

        $licenceVehicle = new LicenceVehicle($licence, $vehicle);
        if ($command->getSpecifiedDate() !== null) {
            $licenceVehicle->setSpecifiedDate(new \DateTime($command->getSpecifiedDate()));
        }
        if ($command->getReceivedDate() !== null) {
            $licenceVehicle->setReceivedDate(new \DateTime($command->getReceivedDate()));
        }

        $this->getRepo('LicenceVehicle')->save($licenceVehicle);

        $result->addId('licenceVehicle', $licenceVehicle->getId());
        $result->addMessage('Licence Vehicle created');

        return $result;
    }

    /**
     * Check if the vrm exists on any other licences
     */
    protected function checkForConfirmation(LicenceEntity $licence, Cmd $command)
    {
        $licences = $this->getRepo()->fetchByVrm($command->getVrm(), true);

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
                $refs[] = $this->getLicNo($otherLicence);
            }

            $message = json_encode($refs);
        } else {
            $message = 'Vehicle exists on other licence';
        }

        throw new RequiresConfirmationException($message, Vehicle::ERROR_VRM_OTHER_LICENCE);
    }

    /**
     * Check if the vrm exists on any other licences
     */
    protected function identifyDuplicates($duplicates)
    {
        if (empty($duplicates)) {
            return;
        }

        if ($this->isGranted(Permission::INTERNAL_USER)) {

            $refs = [];

            /** @var LicenceVehicle $duplicate */
            foreach ($duplicates as $duplicate) {
                $refs[] = $this->getLicNo($duplicate->getLicence());
            }

            $message = json_encode($refs);
        } else {
            $message = 'Vehicle exists on other licence';
        }

        throw new RequiresConfirmationException($message, Vehicle::ERROR_VRM_OTHER_LICENCE);
    }

    protected function getLicNo(LicenceEntity $licence)
    {
        $licNo = $licence->getLicNo();

        if (empty($licNo)) {
            $applications = $licence->getApplications();
            if ($applications->count() > 0) {
                $licNo = 'APP-' . $applications->first()->getId();
            }
        }

        return $licNo;
    }

    /**
     * Check whether the VRM already exist on this licence
     *
     * @param LicenceEntity $licence Licence
     * @param string        $vrm     VRM
     *
     * @return void
     * @throws ValidationException
     */
    protected function checkIfVrmAlreadyExistsOnLicence(LicenceEntity $licence, $vrm)
    {
        $currentLicenceVehicles = $licence->getActiveVehicles(false);

        if ($currentLicenceVehicles->count() < 1) {
            return;
        }

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($currentLicenceVehicles as $licenceVehicle) {
            if ($licenceVehicle->getVehicle()->getVrm() == $vrm) {
                throw new ValidationException(
                    [
                        'vrm' => [
                            Vehicle::ERROR_VRM_EXISTS => 'application.vehicle.already-exist',
                        ],
                    ]
                );
            }
        }
    }

    /**
     * Check if a vehicle has a section 26
     *
     * @param string $vrm
     * @throws ValidationException
     */
    protected function checkIfVrmIsSection26($vrm)
    {
        $vehicles = $this->getRepo('Vehicle')->fetchByVrm($vrm);
        /* @var $vehicle Vehicle */
        foreach ($vehicles as $vehicle) {
            if ($vehicle->getSection26()) {
                throw new ValidationException(
                    [
                        Vehicle::ERROR_VRM_HAS_SECTION_26
                    ]
                );
            }
        }
    }
}
