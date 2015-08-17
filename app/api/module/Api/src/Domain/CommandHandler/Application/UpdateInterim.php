<?php

/**
 * Update Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\UpdateInterim as Cmd;

/**
 * Update Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateInterim extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_REQUIRED = 'Value is required and can\'t be empty';

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['GoodsDisc', 'Fee'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $currentStatusId = $application->getCurrentInterimStatus();

        $requestedOrGranted = [
            ApplicationEntity::INTERIM_STATUS_GRANTED,
            ApplicationEntity::INTERIM_STATUS_REQUESTED
        ];

        // If Requested
        if ($currentStatusId === null || in_array($currentStatusId, $requestedOrGranted)) {
            $this->processStatusRequested($application, $command);
            return $this->result;
        }

        // If Refused or Revoked, can only update status
        $refuseOrRevoke = [
            ApplicationEntity::INTERIM_STATUS_REFUSED,
            ApplicationEntity::INTERIM_STATUS_REVOKED
        ];

        if (in_array($currentStatusId, $refuseOrRevoke)) {
            $application->setInterimStatus($this->getRepo()->getRefdataReference($command->getStatus()));
            $this->getRepo()->save($application);
            $this->result->addMessage('Interim status updated');
            return $this->result;
        }

        if ($currentStatusId === ApplicationEntity::INTERIM_STATUS_INFORCE) {
            $this->saveInterimData($application, $command, true);
            return $this->result;
        }

        return $this->result;
    }

    protected function processStatusRequested(ApplicationEntity $application, Cmd $command)
    {
        // Create fee if selecting Y and doesn't currently have a statue
        // Need to do this before calling saveInterimData, as that method updates the status
        $shouldCreateFee = $command->getRequested() === 'Y' && $application->getInterimStatus() === null;
        $shouldRemoveFee = $command->getRequested() !== 'Y';

        $this->saveInterimData($application, $command);

        if ($shouldCreateFee) {
            $this->maybeCreateInterimFee($application);
        } elseif ($shouldRemoveFee) {
            $this->maybeCancelInterimFee($application);
        }
    }

    protected function saveInterimData(ApplicationEntity $application, Cmd $command, $ignoreRequested = false)
    {
        $this->validateDate($command);

        $status = null;

        // If we are attempting to set the status
        if ($command->getStatus() !== null) {
            $status = $this->getRepo()->getRefdataReference($command->getStatus());
        }

        $processInForce = ($application->getCurrentInterimStatus() === ApplicationEntity::INTERIM_STATUS_INFORCE);

        if ($ignoreRequested || $command->getRequested() == 'Y') {

            if ($status === null && $application->getCurrentInterimStatus() === null) {
                $status = $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_REQUESTED);
            }

            $application->setInterimReason($command->getReason());
            $application->setInterimStart(new DateTime($command->getStartDate()));
            $application->setInterimEnd(new DateTime($command->getEndDate()));
            $application->setInterimAuthVehicles($command->getAuthVehicles());
            $application->setInterimAuthTrailers($command->getAuthTrailers());

            if ($status !== null) {
                $application->setInterimStatus($status);
            }

            $interimOcs = $command->getOperatingCentres() !== null ? $command->getOperatingCentres() : [];
            $interimVehicles = $command->getVehicles() !== null ? $command->getVehicles() : [];
            $this->result->addMessage('Interim data updated');
        } else {

            $application->setInterimReason(null);
            $application->setInterimStart(null);
            $application->setInterimEnd(null);
            $application->setInterimAuthVehicles(null);
            $application->setInterimAuthTrailers(null);
            $application->setInterimStatus(null);

            $interimOcs = [];
            $interimVehicles = [];
            $this->result->addMessage('Interim data reset');
        }

        $this->saveApplictionOperatingCentresForInterim($application, $interimOcs);
        $this->saveVehiclesForInterim($application, $interimVehicles, $processInForce);

        $this->getRepo()->save($application);
    }

    protected function saveApplictionOperatingCentresForInterim(ApplicationEntity $application, array $interimOcs)
    {
        /** @var ApplicationOperatingCentre[] $operatingCentres */
        $operatingCentres = $application->getOperatingCentres();

        /** @var ApplicationOperatingCentre $aoc */
        foreach ($operatingCentres as $aoc) {
            // If was interim, but not in the list
            if ($aoc->getIsInterim() === 'Y' && !in_array($aoc->getId(), $interimOcs)) {
                $aoc->setIsInterim('N');
            } elseif ($aoc->getIsInterim() === 'N' && in_array($aoc->getId(), $interimOcs)) {
                $aoc->setIsInterim('Y');
            }
        }
    }

    protected function saveVehiclesForInterim(ApplicationEntity $application, array $interimVehicles, $processInForce)
    {
        /** @var LicenceVehicle[] $licenceVehicles */
        $licenceVehicles = $application->getLicenceVehicles();

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($licenceVehicles as $licenceVehicle) {
            // No longer interim
            if ($licenceVehicle->getInterimApplication() !== null
                && !in_array($licenceVehicle->getId(), $interimVehicles)
            ) {

                $licenceVehicle->setInterimApplication(null);

                if ($processInForce) {
                    $licenceVehicle->setSpecifiedDate(null);

                    // Cease active discs
                    $this->ceaseActiveDiscsForVehicle($licenceVehicle);
                }

            } elseif ($licenceVehicle->getInterimApplication() === null
                && in_array($licenceVehicle->getId(), $interimVehicles)
            ) {
                $licenceVehicle->setInterimApplication($application);

                if ($processInForce) {
                    $licenceVehicle->setSpecifiedDate(new DateTime());

                    $goodsDisc = new GoodsDisc($licenceVehicle);
                    $goodsDisc->setIsInterim('Y');

                    $this->getRepo('GoodsDisc')->save($goodsDisc);
                }
            }
        }
    }

    protected function ceaseActiveDiscsForVehicle(LicenceVehicle $licenceVehicle)
    {
        /** @var GoodsDisc $disc */
        foreach ($licenceVehicle->getGoodsDiscs() as $disc) {
            if ($disc->getCeasedDate() === null) {
                $disc->setCeasedDate(new DateTime());
            }
        }
    }

    protected function validateDate(Cmd $command)
    {
        if ($command->getRequested() !== 'Y') {
            return;
        }

        $messages = [];

        $reason = $command->getReason();

        if (empty($reason)) {
            $messages['reason'][self::ERR_REQUIRED] = self::ERR_REQUIRED;
        }

        if ($command->getStartDate() === null) {
            $messages['startDate'][self::ERR_REQUIRED] = self::ERR_REQUIRED;
        }

        if ($command->getEndDate() === null) {
            $messages['endDate'][self::ERR_REQUIRED] = self::ERR_REQUIRED;
        }

        $authVehicles = $command->getAuthVehicles();

        if (empty($authVehicles)) {
            $messages['authVehicles'][self::ERR_REQUIRED] = self::ERR_REQUIRED;
        }

        if ($command->getAuthTrailers() === null) {
            $messages['authTrailers'][self::ERR_REQUIRED] = self::ERR_REQUIRED;
        }

        if (!empty($messages)) {
            throw new ValidationException($messages);
        }
    }

    /**
     * Create interim fee if needed
     *
     * @param ApplicationEntity $application
     * @return array
     */
    protected function maybeCreateInterimFee(ApplicationEntity $application)
    {
        $fees = $this->getExistingFees($application);

        if (empty($fees)) {

            $data = [
                'id' => $application->getId(),
                'feeTypeFeeType' => FeeType::FEE_TYPE_GRANTINT
            ];

            $this->result->merge($this->handleSideEffect(CreateApplicationFeeCmd::create($data)));
        }
    }

    /**
     * Cancel interim fee if needed
     *
     * @params ApplicationEntity $application
     * @return array
     */
    protected function maybeCancelInterimFee(ApplicationEntity $application)
    {
        // get fees if exists
        $fees = $this->getExistingFees($application);

        /** @var Fee $fee */
        foreach ($fees as $fee) {
            $fee->setFeeStatus($this->getRepo()->getRefdataReference(Fee::STATUS_CANCELLED));
            $this->getRepo('Fee')->save($fee);
        }
    }

    /**
     * Get existing grant fees
     *
     * @param ApplicationEntity $application
     * @return array
     */
    protected function getExistingFees(ApplicationEntity $application)
    {
        return $this->getRepo('Fee')->fetchInterimFeesByApplicationId($application->getId(), true);
    }
}
