<?php

/**
 * Delete Operating Centre Transport Manager Links
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\OperatingCentre;

use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteTmLinks as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete Operating Centre Transport Manager Links
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class DeleteTmLinks extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'OperatingCentre';

    protected $extraRepos = [
        'TransportManagerApplication',
        'TransportManagerLicence',
    ];

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var OperatingCentreEntity $operatingCentre */
        $operatingCentre = $command->getOperatingCentre();

        $count = 0;
        foreach ($operatingCentre->getTransportManagerLicences() as $tmLicence) {
            $tmLicence->getOperatingCentres()->removeElement($operatingCentre);
            $this->getRepo('TransportManagerLicence')->save($tmLicence);
            $count++;
        }
        $this->result->addMessage(
            sprintf(
                'Delinked %d TransportManagerLicence record(s) from Operating Centre %d',
                $count,
                $operatingCentre->getId()
            )
        );

        $count = 0;
        foreach ($operatingCentre->getTransportManagerApplications() as $tmApplication) {
            if ($tmApplication->getApplication()->isUnderConsideration()) {
                $tmApplication->getOperatingCentres()->removeElement($operatingCentre);
                $this->getRepo('TransportManagerApplication')->save($tmApplication);
                $count++;
            }
        }
        $this->result->addMessage(
            sprintf(
                'Delinked %d TransportManagerApplication record(s) from Operating Centre %d',
                $count,
                $operatingCentre->getId()
            )
        );

        return $this->result;
    }
}
