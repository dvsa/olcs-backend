<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="txn",
 *    indexes={
 *        @ORM\Index(name="ix_txn_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_txn_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_txn_status", columns={"status"}),
 *        @ORM\Index(name="ix_txn_waive_recommender_user_id", columns={"waive_recommender_user_id"}),
 *        @ORM\Index(name="ix_txn_processed_by_user_id", columns={"processed_by_user_id"}),
 *        @ORM\Index(name="ix_txn_olbs_key", columns={"olbs_key"}),
 *        @ORM\Index(name="ix_txn_payment_method", columns={"payment_method"}),
 *        @ORM\Index(name="ix_txn_type", columns={"type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_txn_receipt_document_id", columns={"receipt_document_id"})
 *    }
 * )
 */
class Transaction extends AbstractTransaction
{
    const STATUS_OUTSTANDING = 'pay_s_os';
    const STATUS_CANCELLED = 'pay_s_cn';
    const STATUS_FAILED = 'pay_s_fail';
    // 'paid' and 'complete' are synonymous
    const STATUS_PAID = 'pay_s_pd';
    const STATUS_COMPLETE = 'pay_s_pd';

    const TYPE_WAIVE = 'trt_waive';
    const TYPE_PAYMENT = 'trt_payment';
    const TYPE_REFUND = 'trt_refund';
    const TYPE_REVERSAL = 'trt_reversal';

    /**
     * @return boolean
     */
    public function isOutstanding()
    {
        return $this->getStatus()->getId() === self::STATUS_OUTSTANDING;
    }

    /**
     * @return boolean
     * @deprecated
     */
    public function isPaid()
    {
        return $this->getStatus()->getId() === self::STATUS_PAID;
    }

    /**
     * @return boolean
     */
    public function isComplete()
    {
        return $this->getStatus()->getId() === self::STATUS_COMPLETE;
    }

    public function getTotalAmount()
    {
        $total = 0;

        $this->getFeeTransactions()->forAll(
            function ($key, $ft) use (&$total) {
                unset($key); // unused
                $total += $ft->getAmount();
                return true;
            }
        );

        return number_format($total, 2, '.', '');
    }

    public function getCalculatedBundleValues()
    {
        return [
            'amount' => $this->getTotalAmount(),
            'displayReversalOption' => $this->displayReversalOption(),
            'canReverse' => $this->canReverse(),
        ];
    }

    /**
     * Small helper to get array of feeTransaction IDs, useful for logging
     */
    public function getFeeTransactionIds()
    {
        $ftIds = [];

        if (!empty($this->getFeeTransactions())) {
            $ftIds = array_map(
                function ($ft) {
                    return $ft->getId();
                },
                $this->getFeeTransactions()->toArray()
            );
        }

        return $ftIds;
    }

    /**
     * @return boolean
     */
    public function isWaive()
    {
        return $this->getType()->getId() === self::TYPE_WAIVE;
    }

    /**
     * @return boolean
     */
    public function isPayment()
    {
        return $this->getType()->getId() === self::TYPE_PAYMENT;
    }

    /**
     * @return array
     */
    public function getFeeTransactionsForReversal()
    {
        $feeTransactions = [];

        foreach ($this->getFeeTransactions() as $ft) {
            if (!$ft->isRefundedOrReversed()) {
                $feeTransactions[] = $ft;
            }
        }

        return $feeTransactions;
    }

    /**
     * Determine whether to show the 'Reverse' option for a transaction
     *
     * Note: there are additional checks for whether a transaction can ultimately be reversed
     * @see canReverse()
     */
    public function displayReversalOption()
    {
        return ($this->isPayment() && $this->isComplete());
    }

    /**
     * Determine whether a transaction is reversable, i.e.
     * none of the allocated fee amounts has already been refunded or reversed
     *
     * @return bool
     */
    public function canReverse()
    {
        // reuse displayReversalOption logic for initial checks
        if (!$this->displayReversalOption()) {
            return false;
        }

        foreach ($this->getFeeTransactions() as $ft) {
            if ($ft->isRefundedOrReversed()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getProcessedByLoginId()
    {
        if ($this->getProcessedByUser()) {
            return $this->getProcessedByUser()->getLoginId();
        }
    }
}
