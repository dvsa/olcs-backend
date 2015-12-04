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
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
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

        try {
            $response = $this->getCpmsService()->adjustTransaction(
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
        foreach ($originalTransaction->getFeeTransactionsForReversal() as $originalFt) {
            $feeTransaction = new FeeTransactionEntity();
            $reversalAmount = $originalFt->getAmount() * -1;
            $fee = $originalFt->getFee();
            $feeTransaction
                ->setFee($fee)
                ->setTransaction($newTransaction)
                ->setAmount($reversalAmount)
                ->setReversedFeeTransaction($originalFt);
            $newTransaction->getFeeTransactions()->add($feeTransaction);
            $fees[$fee->getId()] = $fee;
        }

        // work out the allocation of the payment amount to fees, will create
        // balancing entry to handle any overpayment
        $allocations = $this->allocatePayments($command->getReceived(), $fees);

        // create new feeTransaction record(s)
        foreach ($fees as &$fee) {

            // @todo
            // $result->merge($this->maybeCancelPendingWaive($fee));

            $allocatedAmount = $allocations[$fee->getId()];
            // @todo check that getOutstandingAmount takes account of reversals above
            $markAsPaid = ($allocatedAmount === $fee->getOutstandingAmount() && !$fee->isPaid());
            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($allocatedAmount)
                ->setTransaction($newTransaction); // needed for cascade persist to work
            $newTransaction->getFeeTransactions()->add($feeTransaction);

            if ($markAsPaid) {
                $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID));
                $method = $newTransaction->getPaymentMethod()->getDescription();
                $this->result->addMessage('Fee ID ' . $fee->getId() . ' updated as paid by ' . $method);
                // We need to call save() on the fee, it won't cascade persist from the transaction
                $this->getRepo('Fee')->save($fee);
                $this->result->merge($this->handleSideEffect(PayFeeCmd::create(['id' => $fee->getId()])));
            }
        }

        // persist transaction
        $this->getRepo()->save($newTransaction);

        $this->result
            ->addId('transaction', $newTransaction->getId())
            ->addMessage('Transaction record created: ' . $newTransaction->getReference())
            ->addId('feeTransaction', $newTransaction->getFeeTransactionIds())
            ->addMessage('FeeTransaction record(s) created');

        return $this->result;

        // @todo overpayment balancing fee
        // @todo reset $fees
    }

    private function validate(CommandInterface $command, TransactionEntity $transaction)
    {
        $changes = [];

        if ($command->getReceived() != $transaction->getTotalAmount()) {
            $changes[] = 'received';
        }
        if ($command->getPayer() != $transaction->getPayerName()) {
            $changes[] = 'payer';
        }
        if ($command->getSlipNo() != $transaction->getPayingInSlipNumber()) {
            $changes[] = 'slipNo';
        }
        if ($command->getChequeNo() && $command->getChequeNo() != $transaction->getChequePoNumber()) {
            $changes[] = 'chequeNo';
        }
        if ($command->getPoNo() && $command->getPoNo() != $transaction->getChequePoNumber()) {
            $changes[] = 'poNo';
        }
        if ($command->getChequeDate() != $transaction->getChequePoDate()) {
            $changes[] = 'chequeDate';
        }

        if (empty($changes)) {
            throw new ValidationException(['You haven\'t changed anything']);
        }
    }

    /**
     * @param string $receivedAmount
     * @param array $fees - passed by reference as we may need to append
     * @return array
     */
    private function allocatePayments($receivedAmount, &$fees)
    {
        // $feeResult = $this->maybeCreateOverpaymentFee($receivedAmount, $fees);

        // if ($feeResult->getId('fee')) {
        //     // an overpayment balancing fee was created, add it to the list
        //     $fees[] = $this->getRepo('Fee')->fetchById($feeResult->getId('fee'));
        // }

        // $this->result->merge($feeResult);

        return $this->feesHelper->allocatePayments($receivedAmount, $fees);
    }
}
