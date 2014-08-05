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
 *    }
 * )
 */
class FeePayment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Fee")
     * @ORM\JoinColumn(name="fee_id", referencedColumnName="id")
     */
    protected $fee;

    /**
     * Identifier - Payment
     *
     * @var \Olcs\Db\Entity\Payment
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    protected $payment;

    /**
     * Set the fee
     *
     * @param \Olcs\Db\Entity\Fee $fee
     * @return \Olcs\Db\Entity\FeePayment
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get the fee
     *
     * @return \Olcs\Db\Entity\Fee
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set the payment
     *
     * @param \Olcs\Db\Entity\Payment $payment
     * @return \Olcs\Db\Entity\FeePayment
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
