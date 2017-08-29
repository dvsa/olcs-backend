<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    /**
     * Handle Command
     *
     * @param \Dvsa\Olcs\Transfer\Command\Licence\CreateGoodsVehicle $command Command
     *
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Entity\Licence\Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        // check, If we have enough vehicles
        $remainingSpaces = $licence->getRemainingSpaces();
        if ($remainingSpaces < 1) {
            throw new ValidationException(
                [
                    'vrm' => [
                        Vehicle::ERROR_TOO_MANY => 'The number of vehicles will exceed the total authorisation'
                    ]
                ]
            );
        }

        //  create vehicle
        $dtoData = $command->getArrayCopy();
        $dtoData['licence'] = $command->getId();
        if ($command->getSpecifiedDate() === null) {
            $dtoData['specifiedDate'] = (new DateTime('now'))->format(\DateTime::ISO8601);
        }
        $dtoData['identifyDuplicates'] = true;

        $vehicleResult = $this->handleSideEffect(
            DomainCmd\Vehicle\CreateGoodsVehicle::create($dtoData)
        );
        $this->result->merge($vehicleResult);

        //  create discs
        $licenceVehicleId = $vehicleResult->getId('licenceVehicle');

        $this->result->merge(
            $this->handleSideEffect(
                DomainCmd\Vehicle\CreateGoodsDiscs::create(
                    [
                        'ids' => [$licenceVehicleId],
                        'isCopy' => 'N',
                    ]
                )
            )
        );

        return $this->result;
    }
}
