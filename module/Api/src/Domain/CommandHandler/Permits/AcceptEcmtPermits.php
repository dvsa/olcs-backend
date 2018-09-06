<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\DeclineEcmtPermits as DeclineEcmtPermitsCmd;

/**
 * Withdraw an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class AcceptEcmtPermits extends AbstractCommandHandler
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
         * @var DeclineEcmtPermitsCmd $command
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);

        //@todo this needs a new status 'declined'
        $newStatus = $this->refData(EcmtPermitApplication::PERMIT_VALID);
        $application->decline($newStatus);

        $this->getRepo()->save($application);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('ECMT permits issued');

        return $result;
    }
}
