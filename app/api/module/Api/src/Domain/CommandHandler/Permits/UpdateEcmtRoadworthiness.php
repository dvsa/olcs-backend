<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtRoadworthiness as UpdateEcmtRoadworthinessCmd;

/**
 * Update ECMT Roadworthiness
 */
final class UpdateEcmtRoadworthiness extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var $ecmtApplication EcmtPermitApplication
         * @var $command UpdateEcmtRoadworthinessCmd
         */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command);

        $ecmtApplication->updateRoadworthiness($command->getRoadworthiness());

        $this->getRepo()->save($ecmtApplication);

        $this->result->addId('id', $ecmtApplication->getId());
        $this->result->addMessage('ECMT Permit Application roadworthiness updated');

        return $this->result;
    }
}
