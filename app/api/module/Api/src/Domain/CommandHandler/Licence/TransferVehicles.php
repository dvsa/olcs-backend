<?php

/**
 * Transfer Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Transfer Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class TransferVehicles extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $licenceVehicleIds = $command->getLicenceVehicles();

        /** @var Licence $targetLicence */
        $targetLicence = $this->getRepo()->fetchById($command->getTarget());

        $newVehicleCount = $targetLicence->getActiveVehiclesCount() + count($licenceVehicleIds);

        if ($newVehicleCount > $targetLicence->getTotAuthVehicles()) {
            throw new ValidationException(
                [
                    Licence::ERROR_TRANSFER_TOT_AUTH => 'Total number of vehicles will exceed the total auth'
                ]
            );
        }

        // Check if vehicles already exist on licence
        die('todo');

        /** @var Licence $sourceLicence */
        //$sourceLicence = $this->getRepo()->fetchUsingId($command);



        return $result;
    }
}
