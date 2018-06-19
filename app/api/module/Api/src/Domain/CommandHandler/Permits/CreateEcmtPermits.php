<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermits;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermits extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermits';
    protected $extraRepos = ['EcmtPermitApplication','Country'];

    public function handleCommand(CommandInterface $command)
    {

        $status = $this->getRepo()->getRefdataReference('lsts_consideration');
        $paymentStatus = $this->getRepo()->getRefdataReference('lfs_ot');

        $ecmtPermitApplication = new EcmtPermitApplication();
        $ecmtPermitApplication->setStatus($status);
        $ecmtPermitApplication->setPaymentStatus($paymentStatus);
        $this->getRepo('EcmtPermitApplication')->save($ecmtPermitApplication);

        $ecmtPermit = new EcmtPermits();

        $ecmtPermit->setStatus($status);
        $ecmtPermit->setPaymentStatus($paymentStatus);
        $ecmtPermit->setEcmtPermitsApplication($ecmtPermitApplication);
        $ecmtPermit->setIntensity($command->getIntensity());

        $countries = $this->buildArrayCollection(Country::class, $command->getCountries());
        $ecmtPermit->addCountrys($countries);

        $this->getRepo()->save($ecmtPermit);

        $result = new Result();
        $result->addId('ecmtPermit', $ecmtPermit->getId());
        $result->addMessage("ECMT permit application ID {$ecmtPermit->getId()} created");

        return $result;

    }
}
