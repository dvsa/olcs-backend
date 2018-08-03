<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermits;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermits extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermits';
    protected $extraRepos = ['EcmtPermitApplication'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermit = $this->createEcmtPermitsObject($command);
        $this->getRepo()->save($ecmtPermit);

        $result = new Result();
        $result->addId('ecmtPermit', $ecmtPermit->getId());
        $result->addMessage("ECMT permit created successfully.");

        return $result;
    }

    /**
     * Create ECMT Permits object
     *
     * @param object $command Command
     *
     * @return object EcmtPermits
     */
    private function createEcmtPermitsObject($command)
    {
        $countries = $this->buildArrayCollection(Country::class, $command->getCountries());

        $ecmtPermitApplication = $this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtPermitsApplication());

        return EcmtPermits::createNew(
            $this->getRepo()->getRefdataReference($command->getStatus()),
            $this->getRepo()->getRefdataReference($command->getPaymentStatus()),
            $ecmtPermitApplication,
            $command->getIntensity(),
            $countries
        );
    }
}
