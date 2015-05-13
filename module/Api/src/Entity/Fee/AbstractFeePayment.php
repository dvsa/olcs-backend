<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeePayment Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee_payment",
 *    indexes={
 *        @ORM\Index(name="ix_fee_payment_payment_id", columns={"payment_id"}),
 *        @ORM\Index(name="ix_fee_payment_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_fee_payment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_payment_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_fee_payment_fee_id_payment_id", columns={"fee_id","payment_id"})
 *    }
 * )
 */
abstract class AbstractFeePayment
{

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Fee
     *
     * @var \Dvsa\Olcs\Api\Entity\Fee\Fee
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Fee\Fee", inversedBy="feePayments")
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
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Payment
     *
     * @var \Dvsa\Olcs\Api\Entity\Fee\Payment
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Fee\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=false)
     */
    protected $payment;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Version
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return FeePayment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return FeePayment
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the fee
     *
     * @param \Dvsa\Olcs\Api\Entity\Fee\Fee $fee
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
     * @return \Dvsa\Olcs\Api\Entity\Fee\Fee
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
     * Set the id
     *
     * @param int $id
     * @return FeePayment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return FeePayment
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return FeePayment
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the payment
     *
     * @param \Dvsa\Olcs\Api\Entity\Fee\Payment $payment
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
     * @return \Dvsa\Olcs\Api\Entity\Fee\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return FeePayment
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
