<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\DeclineEcmtPermits as DeclineEcmtPermitsCmd;

/**
 * Decline an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class DeclineEcmtPermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
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
         * @var DeclineEcmtPermitsCmd $command
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);

        $newStatus = $this->refData(IrhpInterface::STATUS_WITHDRAWN);
        $withdrawReason = $this->refData(WithdrawableInterface::WITHDRAWN_REASON_DECLINED);
        $application->decline($newStatus, $withdrawReason);

        $this->getRepo()->save($application);

        $outstandingFees = $application->getOutstandingFees();
        foreach ($outstandingFees as $fee) {
            $this->result->merge($this->handleSideEffect(CancelFee::create(['id' => $fee->getId()])));
        }

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('ECMT permits declined');

        return $result;
    }
}
