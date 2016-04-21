<?php

/**
 * UpdateForResponsibilities Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;

/**
 * UpdateForResponsibilities Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UpdateForResponsibilities extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $transportManagerApplication =  $this->updateTransportManagerApplication($command);
        $this->getRepo()->save($transportManagerApplication);

        $result->addMessage('Transport Manager Application updated');
        $result->addId('transportManagerApplication', $transportManagerApplication->getId());

        return $result;
    }

    protected function updateTransportManagerApplication($command)
    {
        $transportManagerApplication = $this->getRepo()->
            fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $transportManagerApplication->getOperatingCentres()->clear();
        foreach ($command->getOperatingCentres() as $ocId) {
            $transportManagerApplication->getOperatingCentres()->add(
                $this->getRepo()->getReference(OperatingCentreEntity::class, $ocId)
            );
        }
        $transportManagerApplication->updateTransportManagerApplicationFull(
            $this->getRepo()->getRefdataReference($command->getTmType()),
            $command->getIsOwner(),
            $command->getHoursMon(),
            $command->getHoursTue(),
            $command->getHoursWed(),
            $command->getHoursThu(),
            $command->getHoursFri(),
            $command->getHoursSat(),
            $command->getHoursSun(),
            $command->getAdditionalInformation(),
            $this->getRepo()->getRefdataReference($command->getTmApplicationStatus())
        );
        return $transportManagerApplication;
    }
}
