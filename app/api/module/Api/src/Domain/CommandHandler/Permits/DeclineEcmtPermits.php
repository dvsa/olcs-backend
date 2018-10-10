<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\DeclineEcmtPermits as DeclineEcmtPermitsCmd;

/**
 * Decline an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class DeclineEcmtPermits extends AbstractCommandHandler
{
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

        $newStatus = $this->refData(EcmtPermitApplication::STATUS_WITHDRAWN);
        $application->decline($newStatus);

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
