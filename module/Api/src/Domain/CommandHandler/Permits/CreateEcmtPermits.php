<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\EcmtPermits;
use Dvsa\Olcs\Api\Entity\EcmtPermitCountryLink;
use Dvsa\Olcs\Api\Entity\EcmtPermitApplication;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermits extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermits';
    protected $extraRepos = ['EcmtPermitCountryLink','ApplicationStatus','EcmtPermitApplication','PaymentStatus','Country'];

    public function handleCommand(CommandInterface $command)
    {



        $applicationStatus = $this->getRepo('ApplicationStatus')->fetchById($command->getApplicationStatus());
        $paymentStatus = $this->getRepo('PaymentStatus')->fetchById($command->getPaymentStatus());

        $ecmtPermitApplication = new EcmtPermitApplication();
        $ecmtPermitApplication->setApplicationStatus($applicationStatus);
        $ecmtPermitApplication->setPaymentStatus($paymentStatus);
        $this->getRepo('EcmtPermitApplication')->save($ecmtPermitApplication);

        $ecmtPermit = new EcmtPermits();

        $ecmtPermit->setApplicationStatus($applicationStatus);
        $ecmtPermit->setEcmtPermitsApplication($ecmtPermitApplication);
        $ecmtPermit->setIntensity($command->getIntensity());
        $ecmtPermit->setPaymentStatus($paymentStatus);
        $this->getRepo()->save($ecmtPermit);

        $result = new Result();
        $result->addId('ecmtPermit', $ecmtPermit->getId());
        $result->addMessage("ECMT permit application ID {$command->getCountries()[0]} created");

        foreach($command->getCountries() as $country)
        {
            $countryObj = $this->getRepo('Country')->fetchById($country);

            $ecmtPermitCountryLink = new EcmtPermitCountryLink();
            $ecmtPermitCountryLink->setEcmtPermit($ecmtPermit);
            $ecmtPermitCountryLink->setCountry($countryObj);
            $this->getRepo('EcmtPermitCountryLink')->save($ecmtPermitCountryLink);
        }

        return $result;

    }
}
