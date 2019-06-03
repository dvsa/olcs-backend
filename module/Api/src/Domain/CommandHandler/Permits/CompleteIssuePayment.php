<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\AcceptEcmtPermits as AcceptEcmtPermitsCmd;

/**
 * Update ECMT Permit Application status after sucessfull issuing fee payment.
 *
 * @author Andy NEwton
 */
final class CompleteIssuePayment extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

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
         * @var AcceptEcmtPermitsCmd $command
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);

        $application->completeIssueFee($this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_FEE_PAID));
        $this->getRepo()->save($application);

        $this->result->addId('ecmtPermitApplication', $id);
        $this->result->addMessage('ECMT permit application issue fee completed');

        return $this->result;
    }
}
