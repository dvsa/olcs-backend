<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle $command Command
     *
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        // check, have we enough vehicles
        $remainingSpaces = $application->getRemainingSpaces();
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

        $this->result->merge(
            $this->handleSideEffect(
                DomainCmd\Vehicle\CreateGoodsVehicle::create($dtoData)
            )
        );

        $this->result->merge(
            $this->handleSideEffect(
                DomainCmd\Application\UpdateApplicationCompletion::create(
                    [
                        'id' => $command->getId(),
                        'section' => 'vehicles',
                    ]
                )
            )
        );

        return $this->result;
    }
}
