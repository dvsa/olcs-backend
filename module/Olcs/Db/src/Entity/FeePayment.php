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
 *        @ORM\UniqueConstraint(name="fee_payment_unique", columns={"fee_id","payment_id"})
 *    }
 * )
 */
class FeePayment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Fee", inversedBy="feePayments")
     * @ORM\JoinColumn(name="fee_id", referencedColumnName="id", nullable=false)
     */
    protected $fee;

    /**
     * Fee value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="fee_value", precision=10, scale=2, nullable=true)
     */
    protected $feeValue;

    /**
     * Payment
     *
     * @var \Olcs\Db\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=false)
     */
    protected $payment;

    /**
     * Set the fee
     *
     * @param \Olcs\Db\Entity\Fee $fee
     * @return FeePayment
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
     * Set the fee value
     *
     * @param float $feeValue
     * @return FeePayment
     */
    public function setFeeValue($feeValue)
    {
        $this->feeValue = $feeValue;

        return $this;
    }

    /**
     * Get the fee value
     *
     * @return float
     */
    public function getFeeValue()
    {
        return $this->feeValue;
    }

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
