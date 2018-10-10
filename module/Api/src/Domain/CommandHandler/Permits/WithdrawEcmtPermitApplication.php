<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as WithdrawEcmtPermitApplicationCmd;
use Doctrine\Common\Collections\Criteria;

/**
 * Withdraw an ECMT Permit application
 *
 * @author Scott Callaway
 */
final class WithdrawEcmtPermitApplication extends AbstractCommandHandler
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
         * @var WithdrawEcmtPermitApplicationCmd $command
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);
        $newStatus = $this->refData(EcmtPermitApplication::STATUS_WITHDRAWN);
        $application->withdraw($newStatus);

        $this->getRepo()->save($application);

        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->eq('feeStatus', $this->refData(Fee::STATUS_OUTSTANDING)));
        $criteria->andWhere($criteria->expr()->eq('ecmtPermitApplication', $application));
        foreach ($application->getFees()->matching($criteria) as $fee) {
            $fee->setFeeStatus($this->refData(Fee::STATUS_CANCELLED));
            $this->getRepo('Fee')->save($fee);
        }

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application withdrawn');

        return $result;
    }
}
