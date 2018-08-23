<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
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
    protected $extraRepos = ['Sectors'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $sectorRepo = $this->getRepo('Sectors');

        /**
         * @var $ecmtPermitApplication EcmtPermitApplication
         * @var $command UpdateEcmtPermitApplicationCmd
         */
        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtPermitApplication->setSectors($sectorRepo->getRefdataReference($command->getSectors()));
        $ecmtPermitApplication->setCabotage($command->getCabotage());
        $ecmtPermitApplication->setDeclaration($command->getDeclaration());
        $ecmtPermitApplication->setEmissions($command->getEmissions());
        $ecmtPermitApplication->setPermitsRequired($command->getPermitsRequired());
        $ecmtPermitApplication->setTrips($command->getTrips());
        $ecmtPermitApplication->setInternationalJourneys($command->getInternationalJourneys());
        $ecmtPermitApplication->setDateReceived(new DateTime($command->getDateReceived()));

        $this->getRepo()->save($ecmtPermitApplication);

        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('ECMT Permit Application updated');

        return $result;
    }
}
