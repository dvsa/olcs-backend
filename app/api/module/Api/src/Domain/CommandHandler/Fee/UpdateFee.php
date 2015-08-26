<?php

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateFee extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var FeeEntity $fee */
        $fee = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        switch ($command->getStatus()){
            case FeeEntity::STATUS_WAIVE_RECOMMENDED:
                $result->merge($this->recommendWaive($fee, $command->getWaiveReason()));
                break;
            case FeeEntity::STATUS_WAIVED:
                $result->merge($this->approveWaive($fee, $command->getWaiveReason()));
                break;
            case FeeEntity::STATUS_OUTSTANDING:
                $result->merge($this->cancelWaive($fee));
                break;
        }

        if (in_array($fee->getFeeStatus()->getId(), [FeeEntity::STATUS_WAIVED, FeeEntity::STATUS_PAID])) {
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        return $result;
    }

    /**
     * @param FeeEntity $fee,
     * @param string $reason
     * @return Result
     */
    private function recommendWaive($fee, $reason)
    {
        $now = new DateTime();
        $result = new Result();

        $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_WAIVE_RECOMMENDED));

        $transaction = new TransactionEntity();
        $transaction
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_WAIVE))
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_OUTSTANDING))
            ->setPaymentMethod($this->getRepo()->getRefdataReference(FeeEntity::METHOD_WAIVE))
            ->setComment($reason)
            ->setWaiveRecommendationDate($now)
            ->setWaiveRecommenderUser($this->getCurrentUser());

        $feeTransaction = new FeeTransactionEntity();
        $feeTransaction
            ->setFee($fee)
            ->setTransaction($transaction);
        $fee->getFeeTransactions()->add($feeTransaction);

        // save fee will cascade persist
        $this->getRepo()->save($fee);

        $result
            ->addId('fee', $fee->getId())
            ->addMessage('Fee updated')
            ->addId('transaction', $transaction->getId())
            ->addMessage('Waive transaction created');

        return $result;
    }

    private function approveWaive($fee, $reason)
    {
        $now = new DateTime();
        $result = new Result();

        $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_WAIVED));

        $transaction = $fee->getOutstandingWaiveTransaction();

        if (!$transaction) {
            throw new ValidationException(['pending waive transaction not found']);
        }

        $transaction
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_PAID)) // @todo change to COMPLETE?
            ->setComment($reason)
            ->setCompletedDate($now)
            ->setProcessedByUser($this->getCurrentUser());

        $this->getRepo()->save($fee);

        $result
            ->addId('fee', $fee->getId())
            ->addMessage('Fee updated')
            ->addId('transaction', $transaction->getId())
            ->addMessage('Waive transaction updated');

        return $result;
    }

    private function cancelWaive($fee)
    {
        $now = new DateTime();
        $result = new Result();

        $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_OUTSTANDING));

        $transaction = $fee->getOutstandingWaiveTransaction();

        if (!$transaction) {
             throw new ValidationException(['pending waive transaction not found']);
        }

        $transaction
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_CANCELLED))
            ->setCompletedDate($now)
            ->setProcessedByUser($this->getCurrentUser());

        $this->getRepo()->save($fee);

        $result
            ->addId('fee', $fee->getId())
            ->addMessage('Fee updated')
            ->addId('transaction', $transaction->getId())
            ->addMessage('Waive transaction cancelled');

        return $result;
    }
}
