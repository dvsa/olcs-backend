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
        parent::createService($serviceLocator);
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
        return $this;
    }

    public function handleCommand(CommandInterface $command)
    {
        $originalTransaction = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($command, $originalTransaction);

        $response = $this->adjustInCpms($originalTransaction, $command);

        // create adjustment transaction
        $transactionReference = $response['receipt_reference'];
        $comment = $command->getReason();
        $chequeDate = $command->getChequeDate() ? new \DateTime($command->getChequeDate()) : null;
        $chequePoNumber = $command->getChequeNo() ?: $command->getPoNo();
        $now = new DateTime();
        $newTransaction = new TransactionEntity();
        $newTransaction
            ->setReference($transactionReference)
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

        // add reversal feeTransactions
        $fees = [];
        $previousBalancingFeeId = null;
        foreach ($originalTransaction->getFeeTransactionsForAdjustment() as $originalFt) {
            $this->addReversalFeeTransaction($originalFt, $newTransaction, $fees, $previousBalancingFeeId);
        }

        // work out the allocation of the payment amount to fees, will create
        // balancing entry to handle any overpayment
        $allocations = $this->allocatePayments($command->getReceived(), $fees, $previousBalancingFeeId);

        // create new feeTransaction record(s)
        foreach ($allocations as $feeId => $allocatedAmount) {
            $fee = $fees[$feeId];
            $markAsPaid = ($allocatedAmount === $fee->getOutstandingAmount() && !$fee->isPaid());
            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($allocatedAmount)
                ->setTransaction($newTransaction); // needed for cascade persist to work
            $newTransaction->getFeeTransactions()->add($feeTransaction);

            if ($markAsPaid) {
                $this->markFeeAsPaid($fee, $newTransaction);
            }
        }

        // persist transaction
        $this->getRepo('Transaction')->save($newTransaction);

        $this->result
            ->addId('transaction', $newTransaction->getId())
            ->addMessage('Transaction record created: ' . $newTransaction->getReference())
            ->addId('feeTransaction', $newTransaction->getFeeTransactionIds())
            ->addMessage('FeeTransaction record(s) created');

        // @todo work out which fees to reset
        // $feesToReset = [];
        // $this->result->merge(
        //     $this->handleSideEffect(ResetFeesCmd::create(['fees' => $feesToReset]))
        // );

        return $this->result;
    }

    private function adjustInCpms(TransactionEntity $originalTransaction, CommandInterface $command)
    {
        try {
            return $this->getCpmsService()->adjustTransaction(
                $originalTransaction->getReference(),
                $originalTransaction->getId(),
                $originalTransaction->getFees(),
                $command->getReceived(),
                $command->getPayer(),
                $command->getSlipNo(),
                $command->getChequeNo(),
                $command->getChequeDate(),
                $command->getPoNo()
            );
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
     * @param int                  &$previousBalancingFeeId passed by reference as we update it
     */
    private function addReversalFeeTransaction(
        FeeTransactionEntity $originalFt,
        TransactionEntity $newTransaction,
        array &$fees,
        &$previousBalancingFeeId
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

        if ($fee->isBalancingFee()) {
            // this should get cancelled
            $previousBalancingFeeId = $fee->getId();
        }
    }

    /**
     * Mark a fee as paid
     *
     * @param  FeeEntity         $fee
     * @param  TransactionEntity $newTransaction
     * @return null
     */
    private function markFeeAsPaid(FeeEntity $fee, TransactionEntity $newTransaction)
    {
        $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID));
        $method = $newTransaction->getPaymentMethod()->getDescription();
        // We need to call save() on the fee, it won't cascade persist from the transaction
        $this->getRepo('Fee')->save($fee);
        $this->result->addMessage('Fee ID ' . $fee->getId() . ' updated as paid by ' . $method);
        $this->result->merge($this->handleSideEffect(PayFeeCmd::create(['id' => $fee->getId()])));
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
