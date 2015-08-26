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
    const STATUS_PAID = 'pay_s_pd';

    const TYPE_WAIVE = 'trt_waive';

    /**
     * @return boolean
     */
    public function isOutstanding()
    {
        return $this->getStatus()->getId() === self::STATUS_OUTSTANDING;
    }

    /**
     * @return boolean
     */
    public function isPaid()
    {
        return $this->getStatus()->getId() === self::STATUS_PAID;
    }
}
