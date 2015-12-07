<?php

/**
 * Reset Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Reset Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ResetFees extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Fee';

    /**
     * Given a payment amount and an array of fees, will create an overpayment
     * balancing fee if required.
     */
    public function handleCommand(CommandInterface $command)
    {
        $fees = $command->getFees();

        $outstanding = $this->getRepo()->getRefdataReference(FeeEntity::STATUS_OUTSTANDING);
        $cancelled = $this->getRepo()->getRefdataReference(FeeEntity::STATUS_CANCELLED);

        foreach ($fees as $feeId => $fee) {
            $status = $fee->isBalancingFee() ? $cancelled : $outstanding;
            $fee->setFeeStatus($status);
            $this->getRepo()->save($fee);
            $this->result->addMessage(sprintf('Fee %d reset to %s', $feeId, $status->getDescription()));
        }

        return $this->result;
    }
}
