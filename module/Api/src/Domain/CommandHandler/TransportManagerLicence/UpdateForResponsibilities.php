<?php

/**
 * UpdateForResponsibilities Transport Manager Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;

/**
 * UpdateForResponsibilities Transport Manager Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UpdateForResponsibilities extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerLicence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $transportManagerLicence =  $this->updateTransportManagerLicence($command);
        $this->getRepo()->save($transportManagerLicence);

        $result->addMessage("Transport Manager Licence updated");
        $result->addId('transportManagerLicence', $transportManagerLicence->getId());

        return $result;
    }

    protected function updateTransportManagerLicence($command)
    {
        $transportManagerLicence = $this->getRepo()->
            fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $transportManagerLicence->getOperatingCentres()->clear();
        foreach ($command->getOperatingCentres() as $ocId) {
            $transportManagerLicence->getOperatingCentres()->add(
                $this->getRepo()->getReference(OperatingCentreEntity::class, $ocId)
            );
        }
        $transportManagerLicence->updateTransportManagerLicence(
            $this->getRepo()->getRefdataReference($command->getTmType()),
            $command->getHoursMon(),
            $command->getHoursTue(),
            $command->getHoursWed(),
            $command->getHoursThu(),
            $command->getHoursFri(),
            $command->getHoursSat(),
            $command->getHoursSun(),
            $command->getAdditionalInformation(),
            $command->getIsOwner()
        );
        return $transportManagerLicence;
    }
}
