<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\WithdrawApplicationInterface;

/**
 * Abstract handler to withdraw an application
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
abstract class AbstractWithdrawApplicationHandler extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'changeMe';
    protected $withdrawStatus = IrhpInterface::STATUS_WITHDRAWN; //override for non-permits entities
    protected $confirmationMessage = 'Application withdrawn';
    protected $cancelFees = true;
    protected $emails = []; //map a withdraw status to a confirmation email
    protected $sideEffects = [];

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
         * @var RepositoryInterface          $repo
         * @var WithdrawableInterface        $application
         * @var WithdrawApplicationInterface $command
         */
        $repo = $this->getRepo();
        $id = $command->getId();
        $withdrawReason = $command->getReason();

        $application = $repo->fetchById($id);
        $application->withdraw($this->refData($this->withdrawStatus), $this->refData($withdrawReason));

        $repo->save($application);

        //Optionally cancel outstanding fees, defaults to true
        if ($this->cancelFees) {
            $outstandingFees = $application->getOutstandingFees();

            /** @var Fee $fee */
            foreach ($outstandingFees as $fee) {
                $this->sideEffects[] = CancelFee::create(['id' => $fee->getId()]);
            }
        }

        //optional email based on the withdraw reason
        if (isset($this->emails[$withdrawReason])) {
            $this->sideEffects[] = $this->emailQueue($this->emails[$withdrawReason], ['id' => $id], $id);
        }

        $this->handleSideEffects($this->sideEffects);

        $this->result->addId($this->repoServiceName, $command->getId());
        $this->result->addMessage($this->confirmationMessage);

        return $this->result;
    }
}
