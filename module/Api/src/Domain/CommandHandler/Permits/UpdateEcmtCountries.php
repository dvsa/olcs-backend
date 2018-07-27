<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtApplicationRestrictedCountries;
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

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getCountryIds() as $countryId) {
            Logger::crit(print_r($this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtApplicationId())), true);
            $ecmtRestrictedCountries = $this->createRestrictedCountriesObject($command, $countryId);
            $this->getRepo()->save($ecmtRestrictedCountries);
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
    private function createRestrictedCountriesObject($command, $countryId)
    {
        return EcmtApplicationRestrictedCountries::createNew(
            $this->getRepo('EcmtPermitApplication')->fetchById($command->getEcmtApplicationId()),
            $countryId
        );
    }

}
