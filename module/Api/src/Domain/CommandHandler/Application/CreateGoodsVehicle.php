<?php

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as VehicleCmd;

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $remainingSpaces = $application->getRemainingSpaces();

        // If we already have enough vehicles
        if ($remainingSpaces < 1) {
            throw new ValidationException(
                [
                    'vrm' => [
                        Vehicle::ERROR_TOO_MANY => 'The number of vehicles will exceed the total authorisation'
                    ]
                ]
            );
        }

        $dtoData = $command->getArrayCopy();
        $dtoData['licence'] = $application->getLicence()->getId();
        $dtoData['applicationId'] = $application->getId();

        $vehicleResult = $this->handleSideEffect(VehicleCmd::create($dtoData));
        $result->merge($vehicleResult);

        $licenceVehicle = $this->getRepo('LicenceVehicle')->fetchById($vehicleResult->getId('licenceVehicle'));
        $licenceVehicle->setApplication($application);
        $this->getRepo('LicenceVehicle')->save($licenceVehicle);

        $dtoData = ['id' => $command->getId(), 'section' => 'vehicles'];
        $result->merge($this->handleSideEffect(UpdateApplicationCompletionCmd::create($dtoData)));

        return $result;
    }
}
