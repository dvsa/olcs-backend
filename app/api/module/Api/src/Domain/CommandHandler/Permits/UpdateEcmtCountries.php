<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtApplicationRestrictedCountries;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository;

use Olcs\Logging\Log\Logger;

/**
 * Update ECMT Restricted Countries
 *
 * @author Scott Callaway
 */
final class UpdateEcmtCountries extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtApplicationRestrictedCountries';

    protected $extraRepos = ['Country', 'EcmtPermitApplication'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $countrys = [];

        foreach ($command->getCountryIds() as $countryId) {
            $countrys[] = $this->getRepo('Country')->getReference(Country::class, $countryId);
        }

        $application = $this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtApplicationId());
        $application->setCountrys($countrys);

        $this->getRepo('EcmtPermitApplication')->save($application);
        $result->addMessage('ECMT Permit Application Restricted Countries updated');

        return $result;
    }

  /**
   * Create EcmtRestritedCountries object
   *
   * @param Cmd $command Command
   * @param int $countryId Country Id
   *
   * @return EcmtApplicationRestrictedCountries
   */
    private function createRestrictedCountriesObject($applicationRef, $countryRef)
    {
        return EcmtApplicationRestrictedCountries::createNew(
            $applicationRef,
            $countryRef
        );
    }
}
