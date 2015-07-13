<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationOperatingCentre;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Class CreateApplicationOperatingCentre
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationOperatingCentre
 */
final class CreateApplicationOperatingCentre extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ApplicationOperatingCentre';

    public function handleCommand(CommandInterface $command)
    {
        $applicationOperatingCentre = new ApplicationOperatingCentre(
            $command->getApplication(),
            $command->getOperatingCentre()
        );

        $applicationOperatingCentre->setAction(ApplicationOperatingCentre::ACTION_ADD);
        $applicationOperatingCentre->setAdPlaced('N');
        $applicationOperatingCentre->setNoOfVehiclesRequired($command->getNoOfVehiclesRequired());
        $applicationOperatingCentre->setNoOfTrailersRequired($command->getNoOfTrailersRequired());
        $applicationOperatingCentre->getS4($command->getS4());

        $this->getRepo()->save($applicationOperatingCentre);

        $result = new Result();
        $result->addMessage('Application operating centre saved.');

        return $result;
    }
}