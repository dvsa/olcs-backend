<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create default irhp permit applications
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CreateDefaultIrhpPermitApplications extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitApplication', 'IrhpPermitWindow'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();

        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);
        $permitTypeId = $irhpApplication->getIrhpPermitType()->getId();

        if ($permitTypeId != IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL) {
            $this->result->addMessage('No default irhp permit applications need to be created');
            return $this->result;
        }

        $irhpPermitWindows = $this->getRepo('IrhpPermitWindow')->fetchOpenWindowsByType(
            $permitTypeId,
            new DateTime()
        );

        $irhpPermitApplicationRepo = $this->getRepo('IrhpPermitApplication');

        foreach ($irhpPermitWindows as $irhpPermitWindow) {
            $irhpPermitApplication = IrhpPermitApplication::createNewForIrhpApplication(
                $irhpApplication,
                $irhpPermitWindow
            );

            $irhpPermitApplicationRepo->save($irhpPermitApplication);
        }

        $this->result->addMessage('Created ' . count($irhpPermitWindows) . ' irhp permit applications');
        return $this->result;
    }
}
