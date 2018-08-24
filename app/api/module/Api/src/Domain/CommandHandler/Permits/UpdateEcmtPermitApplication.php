<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtPermitApplication as UpdateEcmtPermitApplicationCmd;

/**
 * Update ECMT Permit Application
 *
 * @author Andy Newton
 */
final class UpdateEcmtPermitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['Sectors', 'Country'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var $ecmtPermitApplication EcmtPermitApplication
         * @var $command UpdateEcmtPermitApplicationCmd
         */

        $countrys = [];
        foreach ($command->getCountryIds() as $countryId) {
            $countrys[] = $this->getRepo('Country')->getReference(Country::class, $countryId);
        }

        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtPermitApplication->update(
            $ecmtPermitApplication,
            $this->getRepo()->getRefdataReference($command->getPermitType()),
            $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
            $this->getRepo()->getReference(Sectors::class, $command->getSectors()),
            $countrys,
            $command->getCabotage(),
            $command->getDeclaration(),
            $command->getEmissions(),
            $command->getPermitsRequired(),
            $command->getTrips(),
            $this->getRepo()->getRefdataReference($command->getInternationalJourneys()),
            $command->getDateReceived()
        );

        $this->getRepo()->save($ecmtPermitApplication);

        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('ECMT Permit Application updated');

        return $result;
    }
}
