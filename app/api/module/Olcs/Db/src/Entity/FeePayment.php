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
 *        @ORM\Index(name="IDX_8E9E5C514C3A3BB", columns={"payment_id"}),
 *        @ORM\Index(name="IDX_8E9E5C5165CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_8E9E5C51DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_8E9E5C51AB45AECA", columns={"fee_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="fee_payment_unique", columns={"fee_id","payment_id"})
 *    }
 * )
 */
class FeePayment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\FeeManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Payment
     *
     * @var \Olcs\Db\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Payment", fetch="LAZY")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=false)
     */
    protected $payment;

    /**
     * Fee value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="fee_value", precision=10, scale=2, nullable=true)
     */
    protected $feeValue;

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
}
