<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="payment",
 *    indexes={
 *        @ORM\Index(name="ix_payment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_payment_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_payment_payment_status", columns={"status"})
 *    }
 * )
 */
class Payment extends AbstractPayment
{
    const STATUS_OUTSTANDING = 'pay_s_os';
    const STATUS_CANCELLED = 'pay_s_cn';
    const STATUS_LEGACY = 'pay_s_leg';
    const STATUS_FAILED = 'pay_s_fail';
    const STATUS_PAID = 'pay_s_pd';

    /**
     * @return boolean
     */
    public function isOutstanding()
    {
        return $this->getStatus()->getId() === self::STATUS_OUTSTANDING;
    }
}
