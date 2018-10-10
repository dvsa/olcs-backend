<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as WithdrawEcmtPermitApplicationCmd;

/**
 * Withdraw an ECMT Permit application
 *
 * @author Scott Callaway
 */
final class WithdrawEcmtPermitApplication extends AbstractCommandHandler
{
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
         * @var WithdrawEcmtPermitApplicationCmd $command
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);
        $newStatus = $this->refData(EcmtPermitApplication::STATUS_WITHDRAWN);
        $withdrawReason = $this->refData(EcmtPermitApplication::WITHDRAWN_REASON_BY_USER);
        $application->withdraw($newStatus, $withdrawReason);

        $this->getRepo()->save($application);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application withdrawn');

        return $result;
    }
}
