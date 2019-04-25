<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update ECMT Restricted Countries
 *
 * @author Scott Callaway
 */
final class UpdateEcmtCountries extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtApplicationRestrictedCountries';

    protected $extraRepos = ['Country', 'EcmtPermitApplication'];

    /**
     * Update the ECMT countries
     *
     * @param CommandInterface $command command to update countries
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $countrys = new ArrayCollection();

        foreach ($command->getCountryIds() as $countryId) {
            $countrys->add($this->getRepo('Country')->getReference(Country::class, $countryId));
        }

        /** @var EcmtPermitApplication $application */
        $application = $this->getRepo('EcmtPermitApplication')->fetchById($command->getId());
        $application->updateCountrys($countrys);

        $this->getRepo('EcmtPermitApplication')->save($application);
        $result->addMessage('ECMT Permit Application Restricted Countries updated');

        return $result;
    }
}
