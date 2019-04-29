<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtEmissions as UpdateEcmtEmissionsCmd;

/**
 * Update ECMT Euro 6
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class UpdateEcmtEmissions extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var $ecmtApplication EcmtPermitApplication
         * @var $command UpdateEcmtEmissionsCmd
         */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtApplication->updateEmissions($command->getEmissions());

        $this->getRepo()->save($ecmtApplication);

        $result->addId('ecmtEuro6', $ecmtApplication->getId());
        $result->addMessage('ECMT Permit Application Euro6 updated');

        return $result;
    }
}
