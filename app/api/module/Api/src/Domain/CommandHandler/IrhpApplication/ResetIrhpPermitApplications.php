<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\CreateDefaultIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitApplication\Delete as DeleteIrhpPermitApplicationCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use RuntimeException;

/**
 * Reset irhp permit applications
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ResetIrhpPermitApplications extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitApplication'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo()->fetchById($command->getId());

        $irhpPermitTypeId = $irhpApplication->getIrhpPermitType()->getId();
        switch ($irhpPermitTypeId) {
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
                $this->resetIrhpPermitApplications($irhpApplication->getIrhpPermitApplications());
                break;
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL:
                $this->deleteIrhpPermitApplications($irhpApplication->getIrhpPermitApplications());
                break;
            default:
                throw new RuntimeException(
                    sprintf(
                        'ResetIrhpPermitApplications command does not support permit type %s',
                        $irhpPermitTypeId
                    )
                );
        }

        return $this->result;
    }

    /**
     * Reset existing irhp permit applications to initial state
     *
     * @param mixed $irhpPermitApplications
     */
    private function resetIrhpPermitApplications($irhpPermitApplications)
    {
        $irhpPermitApplicationRepo = $this->getRepo('IrhpPermitApplication');
        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitApplication->clearPermitsRequired();
            $irhpPermitApplicationRepo->save($irhpPermitApplication);
        }

        $this->result->addMessage(
            sprintf(
                'Reset %s irhp permit applications to initial state',
                count($irhpPermitApplications)
            )
        );
    }

    /**
     * Delete existing irhp permit applications
     *
     * @param mixed $irhpPermitApplications
     */
    private function deleteIrhpPermitApplications($irhpPermitApplications)
    {
        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $this->result->merge(
                $this->handleSideEffect(
                    DeleteIrhpPermitApplicationCmd::create(['id' => $irhpPermitApplication->getId()])
                )
            );
        }

        $this->result->addMessage(
            sprintf(
                'Deleted %s irhp permit applications',
                count($irhpPermitApplications)
            )
        );
    }
}
