<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="transaction",
 *    indexes={
 *        @ORM\Index(name="ix_transaction_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transaction_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transaction_transaction_status", columns={"status"})
 *    }
 * )
 */
class Transaction extends AbstractTransaction
{
    const STATUS_OUTSTANDING = 'pay_s_os';
    const STATUS_CANCELLED = 'pay_s_cn';
    const STATUS_LEGACY = 'pay_s_leg';
    const STATUS_FAILED = 'pay_s_fail';
    // 'paid' and 'complete' are synonymous
    const STATUS_PAID = 'pay_s_pd';
    const STATUS_COMPLETE = 'pay_s_pd';

    const TYPE_WAIVE = 'trt_waive';
    const TYPE_PAYMENT = 'trt_payment';

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
        ];
    }
}
