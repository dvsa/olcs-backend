<?php

/**
 * Adjust Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateOverpaymentFee as CreateOverpaymentFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\ResetFees as ResetFeesCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Adjust Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class AdjustTransaction extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee'];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        // get original transaction from the db
        $originalTransaction = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // validate the new data against the original transaction
        $this->validate($command, $originalTransaction);

        // create a new adjustment transaction
        $comment = $command->getReason();
        $chequeDate = $command->getChequeDate() ? new \DateTime($command->getChequeDate()) : null;
        $chequePoNumber = $command->getChequeNo() ?: $command->getPoNo();
        $now = new DateTime();
        $newTransaction = new TransactionEntity();
        $newTransaction
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_ADJUSTMENT))
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_COMPLETE))
            ->setCompletedDate($now)
            ->setPaymentMethod($originalTransaction->getPaymentMethod())
            ->setComment($comment)
            ->setPayerName($command->getPayer())
            ->setChequePoNumber($chequePoNumber)
            ->setChequePoDate($chequeDate) // note we don't actually capture date for PO's
            ->setPayingInSlipNumber($command->getSlipNo())
            ->setProcessedByUser($this->getCurrentUser());

        // add 'reversal' feeTransactions, and populate the array of fees
        $fees = [];
        foreach ($originalTransaction->getFeeTransactionsForAdjustment() as $originalFt) {
            $this->addReversalFeeTransaction($originalFt, $newTransaction, $fees);
        }

        // if there was a previous overpayment balancing fee, cancel it
        $this->cancelPreviousBalancingFees($fees);

        // work out the allocation of the new payment amount to the fees; may
        // create a new balancing entry to handle any overpayment
        $allocations = $this->allocatePayments($command->getReceived(), $fees);

        // create new 'positive' feeTransaction record(s) for each new allocation
        foreach ($allocations as $feeId => $allocatedAmount) {
            $fee = $fees[$feeId];

            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($allocatedAmount)
                ->setTransaction($newTransaction); // needed for cascade persist to work

            $newTransaction->getFeeTransactions()->add($feeTransaction);

            // update fee status if required, depending on the new allocation
            $this->maybeChangeFeeStatus($allocatedAmount, $fee, $newTransaction);
        }

        // send the adjustment to CPMS
        $response = $this->adjustInCpms($originalTransaction, $newTransaction);

        // add the CPMS reference to the new transaction
        $newTransaction->setReference($response['receipt_reference']);

        // persist the new transaction
        $this->getRepo('Transaction')->save($newTransaction);

        $this->result
            ->addId('transaction', $newTransaction->getId())
            ->addMessage('Transaction record created: ' . $newTransaction->getReference())
            ->addId('feeTransaction', $newTransaction->getFeeTransactionIds())
            ->addMessage('FeeTransaction record(s) created');

        return $this->result;
    }

    /**
     * @param  TransactionEntity $originalTransaction
     * @param  CommandInterface  $command
     * @return array CPMS response
     * @throws RuntimeException
     */
    private function adjustInCpms(TransactionEntity $originalTransaction, TransactionEntity $newTransaction)
    {
        try {
            return $this->getCpmsService()->adjustTransaction($originalTransaction, $newTransaction);
        } catch (CpmsResponseException $e) {
            // rethrow as Domain exception
            throw new RuntimeException(
                'Error from CPMS service: ' . json_encode($e->getResponse()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Add a 'negative' feeTransaction
     *
     * @param FeeTransactionEntity $originalFt
     * @param TransactionEntity    $newTransaction
     * @param array                &$fees passed by reference as we update it
     */
    private function addReversalFeeTransaction(
        FeeTransactionEntity $originalFt,
        TransactionEntity $newTransaction,
        array &$fees
    ) {
        $feeTransaction = new FeeTransactionEntity();
        $reversalAmount = $originalFt->getAmount() * -1;
        $fee = $originalFt->getFee();
        $feeTransaction
            ->setFee($fee)
            ->setTransaction($newTransaction)
            ->setAmount($reversalAmount)
            ->setReversedFeeTransaction($originalFt);

        // add feeTransaction to the transaction for saving
        $newTransaction->getFeeTransactions()->add($feeTransaction);

        // add feeTransaction to the fee so that outstanding amount
        // is calculated correctly
        $fee->getFeeTransactions()->add($feeTransaction);

        $fees[$fee->getId()] = $fee;
    }

    /**
     * @param  array  &$fees
     */
    private function cancelPreviousBalancingFees(array &$fees)
    {
        $idsToCancel = [];

        foreach ($fees as $feeId => $fee) {
            if ($fee->isBalancingFee()) {
                $idsToCancel[] = $feeId;
            }
        }

        foreach ($idsToCancel as $id) {
            $this->result->merge($this->handleSideEffect(CancelFeeCmd::create(['id' => $id])));
            unset($fees[$id]);
        }
    }

    /**
     * Handles:
     *  - Mark fee as paid if the allocated amount is now sufficient
     *  - Reset fee to outstanding if allocated amount is insufficient
     *  - Reset overpayment fees to cancelled
     *
     * @param  string $allocatedAmount
     * @param  FeeEntity $fee
     * @param  TransactionEntity  $newTransaction
     */
    private function maybeChangeFeeStatus($allocatedAmount, &$fee, $newTransaction)
    {
        if ($allocatedAmount === $fee->getOutstandingAmount() && !$fee->isPaid()) {
            $this->markFeeAsPaid($fee, $newTransaction);
        } elseif ($fee->getOutstandingAmount() > 0 && $fee->isPaid()) {
            $this->resetFee($fee);
        }

    }

    /**
     * Mark a fee as paid
     *
     * @param  FeeEntity         $fee
     * @param  TransactionEntity $newTransaction
     * @return null
     */
    private function markFeeAsPaid(FeeEntity &$fee, TransactionEntity $newTransaction)
    {
        $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID));
        $method = $newTransaction->getPaymentMethod()->getDescription();
        // We need to call save() on the fee, it won't cascade persist from the transaction
        $this->getRepo('Fee')->save($fee);
        $this->result->addMessage('Fee ID ' . $fee->getId() . ' updated as paid by ' . $method);
        $this->result->merge($this->handleSideEffect(PayFeeCmd::create(['id' => $fee->getId()])));
    }

    /**
     * Reset fees that are no longer paid
     * @param FeeEntity $fee
     */
    private function resetFee($fee)
    {
        $this->result->merge(
            $this->handleSideEffect(ResetFeesCmd::create(['fees' => [$fee]]))
        );
    }

    /**
     * Validate the command data
     *
     * @return  boolean (true)
     * @throws  ValidationException if nothing has changed
     */
    public function validate(CommandInterface $command, TransactionEntity $transaction)
    {
        $hasChanged = ($command->getReceived() != $transaction->getTotalAmount())
            || ($command->getPayer() != $transaction->getPayerName())
            || ($command->getSlipNo() != $transaction->getPayingInSlipNumber())
            || ($command->getChequeNo() && $command->getChequeNo() != $transaction->getChequePoNumber())
            || ($command->getPoNo() && $command->getPoNo() != $transaction->getChequePoNumber())
            || ($command->getChequeDate() != $transaction->getChequePoDate());

        if (!$hasChanged) {
            throw new ValidationException(['You haven\'t changed anything']);
        }

        return true;
    }

    /**
     * Allocates the new received amount to the fees, possibly cancelling a
     * previous overpayment fee and possible creating a new one
     *
     * @param string $receivedAmount
     * @param array $fees - passed by reference as we may need to append
     * @param int $previousBalancingFeeId id of fee to cancel
     * @return array
     */
    private function allocatePayments($receivedAmount, &$fees, $previousBalancingFeeId = null)
    {
        if ($previousBalancingFeeId) {
            $this->result->merge(
                $this->handleSideEffect(CancelFeeCmd::create(['id' => $previousBalancingFeeId]))
            );
            unset($fees[$previousBalancingFeeId]);
        }

        $dtoData = [
            'receivedAmount' => $receivedAmount,
            'fees' => $fees,
        ];

        $feeResult = $this->handleSideEffect(CreateOverpaymentFeeCmd::create($dtoData));

        if ($feeResult->getId('fee')) {
            $newFeeId = $feeResult->getId('fee');
            // a new overpayment balancing fee was created, add it to the list
            $fees[$newFeeId] = $this->getRepo('Fee')->fetchById($newFeeId);
        }

        $this->result->merge($feeResult);

        return $this->feesHelper->allocatePayments($receivedAmount, $fees);
    }
}
