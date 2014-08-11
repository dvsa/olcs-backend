<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * FeePayment Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee_payment",
 *    indexes={
 *        @ORM\Index(name="fk_fee_has_payment_payment1_idx", columns={"payment_id"}),
 *        @ORM\Index(name="fk_fee_has_payment_fee1_idx", columns={"fee_id"}),
 *        @ORM\Index(name="fk_fee_payment_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_fee_payment_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="fee_id", columns={"fee_id","payment_id"})
 *    }
 * )
 */
class FeePayment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\FeeManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Payment
     *
     * @var \Olcs\Db\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    protected $payment;


    /**
     * Set the payment
     *
     * @param \Olcs\Db\Entity\Payment $payment
     * @return FeePayment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get the payment
     *
     * @return \Olcs\Db\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

}
