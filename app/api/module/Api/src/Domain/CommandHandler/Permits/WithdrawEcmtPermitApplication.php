<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAutomaticallyWithdrawn;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as WithdrawEcmtPermitApplicationCmd;

/**
 * Withdraw an ECMT Permit application
 *
 * @author Scott Callaway
 */
final class WithdrawEcmtPermitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use QueueAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['Fee'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication            $application
         * @var WithdrawEcmtPermitApplicationCmd $command
         */
        $id = $command->getId();
        $reason = $command->getReason();
        $application = $this->getRepo()->fetchById($id);
        $newStatus = $this->refData(EcmtPermitApplication::STATUS_WITHDRAWN);
        $withdrawReason = $this->refData($reason);
        $application->withdraw($newStatus, $withdrawReason);

        $this->getRepo()->save($application);

        $outstandingFees = $application->getOutstandingFees();
        $sideEffects = [];

        /** @var Fee $fee */
        foreach ($outstandingFees as $fee) {
            $sideEffects[] = CancelFee::create(['id' => $fee->getId()]);
        }

        if ($reason === EcmtPermitApplication::WITHDRAWN_REASON_UNPAID) {
            $sideEffects[] = $this->emailQueue(SendEcmtAutomaticallyWithdrawn::class, ['id' => $id], $id);
        }

        $this->handleSideEffects($sideEffects);
        $this->result->addId('ecmtPermitApplication', $id);
        $this->result->addMessage('Permit application ' . $id . 'withdrawn');

        return $this->result;
    }
}
