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

        $countryRepo = $this->getRepo('Country');
        $applicationRepo = $this->getRepo('EcmtPermitApplication');

        foreach ($command->getCountryIds() as $countryId) {
            $restrictedCountriesObject = EcmtApplicationRestrictedCountries::createNew();
            $restrictedCountriesObject->setEcmtApplication($applicationRepo->getRefdataReference($command->getEcmtApplicationId()));
            $restrictedCountriesObject->setCountry($countryRepo->getRefdataReference($countryId));

            $this->getRepo()->save($restrictedCountriesObject);
        }

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
