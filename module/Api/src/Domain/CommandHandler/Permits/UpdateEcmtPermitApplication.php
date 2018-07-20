<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

/**
 * Update ECMT Permit Application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class UpdateEcmtPermitApplication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermitApplication';
    //protected $extraRepos = ['Licence'];


    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $ecmtPermitApplication EcmtPermitApplication */
        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        if ($command->getLicence() !== null)
        {
            $ecmtPermitApplication->setLicence($this->getRepo('Licence')->fetchById($command->getLicence()));
        }

        if ($command->getStatus() !== null)
        {
            $ecmtPermitApplication->setStatus($this->getRepo()->getRefdataReference($command->getStatus()));
        }

        if ($command->getPermitType() !== null)
        {
            $ecmtPermitApplication->setPermitType($this->getRepo()->getRefdataReference($command->getPermitType()));
        }

        if ($command->getPaymentStatus() !== null)
        {
            $ecmtPermitApplication->setPaymentStatus($this->getRepo()->getRefdataReference($command->getPaymentStatus()));
        }


        if ($command->getEmissions() !== null)
        {
            $ecmtPermitApplication->setEmissions($command->getEmissions());
        }

        if ($command->getCabotage() !== null)
        {
            $ecmtPermitApplication->setCabotage($command->getCabotage());
        }

        if ($command->getDeclaration() !== null)
        {
            $ecmtPermitApplication->setDeclaration($command->getDeclaration());
        }

        if ($command->getInternationalJourneys() !== null)
        {
            $ecmtPermitApplication->setInternationalJourneys($command->getInternationalJourneys());
        }

        if ($command->getNoOfPermits() !== null)
        {
            $ecmtPermitApplication->setNoOfPermits($command->getNoOfPermits());
        }

        if ($command->getPermitsRequired() !== null)
        {
            $ecmtPermitApplication->setPermitsRequired($command->getPermitsRequired());
        }

        if ($command->getSectors() !== null)
        {
            $ecmtPermitApplication->setSectors($command->getSectors());
        }

        if ($command->getTrips() !== null)
        {
            $ecmtPermitApplication->setTrips($command->getTrips());
        }

        $this->getRepo()->save($ecmtPermitApplication);
        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('ECMT Permit Application updated');
        return $result;
    }

}
