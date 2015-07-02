<?php

/**
 * RemoveLicenceVehicle.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class RemoveLicenceVehicle
 *
 * Remove vehicles.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Licence
 */
final class RemoveLicenceVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'LicenceVehicle';

    public function handleCommand(CommandInterface $command)
    {
        $licenceVehicles = $command->getLicenceVehicles()->toArray();

        foreach ($licenceVehicles as $licenceVehicle) {
            $licenceVehicle->setRemovalDate(new \DateTime());

            $this->getRepo()->save($licenceVehicle);
        }

        $result = new Result();
        $result->addMessage('Removed vehicles for licence.');

        return $result;
    }
}
