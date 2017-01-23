<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ConditionUndertaking;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\ConditionUndertaking\Create as CreateConditionUndertakingCmd;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Create small vehicle condition
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateSmallVehicleCondition extends AbstractCommandHandler
{
    protected $repoServiceName = 'ConditionUndertaking';

    protected $extraRepos = ['Application'];

    public function handleCommand(CommandInterface $command)
    {
        $application = $this->getRepo('Application')->fetchById($command->getApplicationId());
        $smallVehicles = [
            ApplicationEntity::PSV_VEHICLE_SIZE_SMALL,
            ApplicationEntity::PSV_VEHICLE_SIZE_BOTH
        ];
        $whichVehicleSizes = $application->getPsvWhichVehicleSizes();
        if ($whichVehicleSizes === null || !in_array($whichVehicleSizes->getId(), $smallVehicles)) {
            return $this->result;
        }
        $conditions = $this->getRepo('ConditionUndertaking')->fetchSmallVehilceUndertakings(
            $application->getLicence()->getId()
        );
        if (count($conditions) > 0) {
            return $this->result;
        }
        $data = [
            'attachedTo' => ConditionUndertaking::ATTACHED_TO_LICENCE,
            'type' => ConditionUndertaking::TYPE_UNDERTAKING,
            'notes' => ConditionUndertaking::SMALL_VEHICLE_UNERRTAKINGS_NOTES,
            'application' => $application->getId()
        ];
        return $this->handleSideEffect(CreateConditionUndertakingCmd::create($data));
    }
}
