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

        /*$countryRepo = $this->getRepo('Country');
        $applicationRepo = $this->getRepo('EcmtPermitApplication');

        $applicationRef = $applicationRepo->getRefdataReference($command->getEcmtApplicationId());
        $applicationRef = $applicationRepo->getReference(EcmtPermitApplication::class, $command->getEcmtApplicationId());

        foreach ($command->getCountryIds() as $countryId) {
            //Logger::crit(print_r($this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtApplicationId())), true);
            $countryRef = $countryRepo->getRefdataReference($countryId);
            $ecmtRestrictedCountries = $this->createRestrictedCountriesObject($applicationRef, $countryRef);
            $this->getRepo()->save($ecmtRestrictedCountries);

        }*/

        $applicationRepo = $this->getRepo('EcmtPermitApplication');
        $countryRepo = $this->getRepo('Country');

        $countryRef = $countryRepo->getRefdataReference('AG');
        $applicationRef = $applicationRepo->getRefdataReference($command->getEcmtApplicationId());

        $restrictedCountry = $this->getRepo()->fetchById(1);

        $restrictedCountry->setEcmtPermitApplication($applicationRef);
        $restrictedCountry->setCountry($countryRef);

        $this->getRepo()->save($restrictedCountry);


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
       /*return EcmtApplicationRestrictedCountries::createNew(
            $this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtApplicationId()),
            $countryId
        );*/
           return EcmtApplicationRestrictedCountries::createNew(
               $applicationRef,
               $countryRef
           );

    }

}
