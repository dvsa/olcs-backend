<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Update ECMT Permit Application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class UpdateEcmtPermitApplication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['Licence'];


    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        if ($command->getLicence())
        {
            $ecmtPermitApplication->setLicence($this->getRepo()->getReference(Licence::class, $command->getLicence()));
        }

        if ($command->getStatus())
        {
            $ecmtPermitApplication->setStatus($this->getRepo()->getRefdataReference($command->getStatus()));
        }

        if ($command->getPermitType())
        {
            $ecmtPermitApplication->setPermitType($this->getRepo()->getRefdataReference($command->getPermitType()));
        }

        if ($command->getCabotage())
        {
            $ecmtPermitApplication->setCabotage($command->getCabotage());
        }

        if ($command->getDeclaration())
        {
            $ecmtPermitApplication->setDeclaration($command->getDeclaration());
        }

        if ($command->getEmissions())
        {
            $ecmtPermitApplication->setEmissions($command->getEmissions());
        }

        if ($command->getInternationalJourneys())
        {
            $ecmtPermitApplication->setInternationalJourneys($command->getInternationalJourneys());
        }

        if ($command->getNoOfPermits())
        {
            $ecmtPermitApplication->setNoOfPermits($command->getNoOfPermits());
        }

        if ($command->getPermitsRequired())
        {
            $ecmtPermitApplication->setPermitsRequired($command->getPermitsRequired());
        }

        if ($command->getSectors())
        {
            $ecmtPermitApplication->setSectors($command->getSectors());
        }

        if ($command->getTrips())
        {
            $ecmtPermitApplication->setTrips($command->getTrips());
        }

        $this->getRepo()->save($ecmtPermitApplication);
        $result->addMessage('ECMT Permit Application updated');
        return $result;
    }

}
