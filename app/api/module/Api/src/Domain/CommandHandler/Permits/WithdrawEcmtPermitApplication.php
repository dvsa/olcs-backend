<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

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
        $application = $this->getRepo()
                        ->fetchById($command->getId());
        $newStatus = $this->getRepo()
                        ->getRefdataReference(EcmtPermitApplication::STATUS_WITHDRAWN);
        $application->withdraw($newStatus);

        $this->getRepo()
            ->save($application);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application withdrawn');

        return $result;
    }
}
