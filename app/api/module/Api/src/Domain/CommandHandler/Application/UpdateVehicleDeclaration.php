<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleDeclaration as Command;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * UpdateVehicleDeclaration
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateVehicleDeclaration extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // validate that the required confirmation have been completed
        $errors = array_merge(
            $this->validateMainOccupation($command, $application),
            $this->validateSmallVehicleIntention($command, $application),
            $this->validateNineOrMore($command, $application),
            $this->validateLimousines($command, $application)
        );
        if (!empty($errors)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException($errors);
        }

        $application->setPsvNoSmallVhlConfirmation($command->getPsvNoSmallVhlConfirmation());
        $application->setPsvOperateSmallVhl($command->getPsvOperateSmallVhl());
        $application->setPsvSmallVhlConfirmation($command->getPsvSmallVhlConfirmation());
        $application->setPsvSmallVhlNotes($command->getPsvSmallVhlNotes());
        $application->setPsvMediumVhlConfirmation($command->getPsvMediumVhlConfirmation());
        $application->setPsvMediumVhlNotes($command->getPsvMediumVhlNotes());
        $application->setPsvLimousines($command->getPsvLimousines());
        $application->setPsvNoLimousineConfirmation($command->getPsvNoLimousineConfirmation());
        $application->setPsvOnlyLimousinesConfirmation($command->getPsvOnlyLimousinesConfirmation());
        $this->getRepo()->save($application);

        $result = $this->handleSideEffect(
            UpdateApplicationCompletionCmd::create(
                ['id' => $application->getId(), 'section' => 'vehicles_declarations']
            )
        );

        $result->addMessage("Application ID {$application->getId()} vehicle declaration updated.");
        return $result;
    }

    /**
     * @param Command $command
     * @param ApplicationEntity $application
     *
     * @return array
     */
    protected function validateMainOccupation(Command $command, ApplicationEntity $application)
    {
        $messages = [];
        // mainOccupation
        if ((int) $application->getTotAuthMediumVehicles() > 0 &&
            $application->getLicenceType()->getId() === LicenceEntity::LICENCE_TYPE_RESTRICTED
            ) {
            if ($command->getPsvMediumVhlConfirmation() !== 'Y') {
                $messages['psvMediumVhlConfirmation'] = 'psvMediumVhlConfirmation must be Y';
            }
            if (empty($command->getPsvMediumVhlNotes())) {
                $messages['psvMediumVhlNotes'] = 'psvMediumVhlNotes must be not be empty';
            }
        }
        return $messages;
    }

    /**
     * @param Command $command
     * @param ApplicationEntity $application
     *
     * @return array
     */
    protected function validateSmallVehicleIntention(Command $command, ApplicationEntity $application)
    {
        $messages = [];
        // smallVehiclesIntention
        if ((int) $application->getTotAuthSmallVehicles() > 0) {
            if ($command->getPsvOperateSmallVhl() === 'Y') {
                if (empty($command->getPsvSmallVhlNotes())) {
                    $messages['psvSmallVhlNotes'] = 'psvSmallVhlNotes must be not be empty';
                }
            } else {
                if ($command->getPsvSmallVhlConfirmation() !== 'Y') {
                    $messages['psvSmallVhlConfirmation'] = 'psvSmallVhlConfirmation must be Y';
                }
            }
        }
        return $messages;
    }

    /**
     * @param Command $command
     * @param ApplicationEntity $application
     *
     * @return array
     */
    protected function validateNineOrMore(Command $command, ApplicationEntity $application)
    {
        $messages = [];
        // noneOrMore
        if ((int) $application->getTotAuthSmallVehicles() === 0) {
            if ($command->getPsvNoSmallVhlConfirmation() !== 'Y') {
                $messages['psvNoSmallVhlConfirmation'] = 'psvNoSmallVhlConfirmation must be Y';
            }
        }
        return $messages;
    }

    /**
     * @param Command $command
     * @param ApplicationEntity $application
     *
     * @return array
     */
    protected function validateLimousines(Command $command, ApplicationEntity $application)
    {
        $messages = [];
        // limousines
        if ($command->getPsvLimousines() === 'Y') {
            if (((int) $application->getTotAuthMediumVehicles() !== 0 ||
                (int) $application->getTotAuthLargeVehicles() !== 0) &&
                $command->getPsvOnlyLimousinesConfirmation() !== 'Y'
            ) {
                $messages['psvOnlyLimousinesConfirmation'] = 'psvOnlyLimousinesConfirmation must be Y';
            }
        } else {
            if ($command->getPsvNoLimousineConfirmation() !== 'Y') {
                $messages['psvNoLimousineConfirmation'] = 'psvNoLimousineConfirmation must be Y';
            }
        }
        return $messages;
    }
}
