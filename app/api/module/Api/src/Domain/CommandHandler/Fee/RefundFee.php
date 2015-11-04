<?php

/**
 * Refund Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Refund Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class RefundFee extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    const REFUND_COMMENT = 'Non over payment refund';

    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        /** @var FeeEntity $fee */
        $fee = $this->getRepo()->fetchUsingId($command);

        try {
            $response = $this->getCpmsService()->batchRefund($fee);
        } catch (CpmsResponseException $e) {
            // rethrow as Domain exception
            throw new RuntimeException(
                'Error from CPMS service: ' . json_encode($e->getResponse()),
                $e->getCode(),
                $e
            );
        }

        $references = $response['receipt_references'];
        // note, we don't record a transaction reference as CPMS returns one per original receipt_reference
        $transactionReference = null;
        $comment = self::REFUND_COMMENT . ' ' . implode(', ', $references);
        $now = new DateTime();

        $transaction = new TransactionEntity();
        $transaction
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_REFUND))
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_COMPLETE))
            ->setPaymentMethod($this->getRepo()->getRefdataReference(FeeEntity::METHOD_REFUND))
            ->setComment($comment)
            ->setCompletedDate($now)
            ->setProcessedByUser($this->getCurrentUser())
            ->setReference($transactionReference);

        foreach ($fee->getFeeTransactionsForRefund() as $originalFt) {

            if (!array_key_exists($originalFt->getTransaction()->getReference(), $references)) {
                // only create records for refunded transactions
                continue;
            }

            $feeTransaction = new FeeTransactionEntity();
            $refundAmount = $originalFt->getAmount() * -1;
            $feeTransaction
                ->setFee($fee)
                ->setTransaction($transaction)
                ->setAmount($refundAmount)
                ->setReversedFeeTransaction($originalFt);
            $fee->getFeeTransactions()->add($feeTransaction);
        }

        // save fee will cascade persist
        $this->getRepo()->save($fee);

        $this->result
            ->addId('transaction', $transaction->getId())
            ->addMessage('Refund transaction created');

        $this->result->merge(
            $this->handleSideEffect(CancelFeeCmd::create(['id' => $fee->getId()]))
        );

        return $this->result;
    }
}
