<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\CancelApplication as CancelIrhpApplication;
use Dvsa\Olcs\Transfer\Command\Permits\CancelEcmtPermitApplication;

/**
 * Close IRHP Permit Window
 */
final class Close extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['EcmtPermitApplication', 'IrhpApplication'];

    /**
     * Handle Close command
     *
     * @param CommandInterface $command Command
     *
     * @return Result
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $window = $this->getRepo()->fetchById($command->getId());

        if (!$window->hasEnded()) {
            throw new ValidationException(['Window which has not ended cannot be closed']);
        }

        // cancel all not yet submitted applications linked to the IRHP permit window
        $windowId = $window->getId();
        $this->cancelAllNotYetSubmittedEcmtPermitApplications($windowId);
        $this->cancelAllNotYetSubmittedIrhpApplications($windowId);

        $this->result->addId('id', $windowId);
        $this->result->addMessage("IRHP permit window '{$windowId}' has been closed");

        return $this->result;
    }

    /**
     * Cancel all not yet submitted ECMT applications linked to the IRHP permit window
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $windowId IRHP permit window id
     *
     * @return void
     */
    private function cancelAllNotYetSubmittedEcmtPermitApplications($windowId)
    {
        $notYetSubmittedApps = $this->getRepo('EcmtPermitApplication')
            ->fetchByWindowId($windowId, [EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED]);

        foreach ($notYetSubmittedApps as $app) {
            $this->result->merge(
                $this->handleSideEffect(
                    CancelEcmtPermitApplication::create(
                        [
                            'id' => $app->getId(),
                        ]
                    )
                )
            );
        }
    }

    /**
     * Cancel all not yet submitted IRHP applications linked to the IRHP permit window
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $windowId IRHP permit window id
     *
     * @return void
     */
    private function cancelAllNotYetSubmittedIrhpApplications($windowId)
    {
        $notYetSubmittedApps = $this->getRepo('IrhpApplication')
            ->fetchByWindowId($windowId, [IrhpInterface::STATUS_NOT_YET_SUBMITTED]);

        foreach ($notYetSubmittedApps as $app) {
            $this->result->merge(
                $this->handleSideEffect(
                    CancelIrhpApplication::create(
                        [
                            'id' => $app->getId(),
                        ]
                    )
                )
            );
        }
    }
}
