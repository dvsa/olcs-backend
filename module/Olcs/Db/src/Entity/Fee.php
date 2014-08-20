<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Fee Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee",
 *    indexes={
 *        @ORM\Index(name="fk_fee_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_fee_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_fee_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_fee_task1_idx", columns={"task_id"}),
 *        @ORM\Index(name="fk_fee_fee_type1_idx", columns={"fee_type_id"}),
 *        @ORM\Index(name="fk_fee_fee1_idx", columns={"parent_fee_id"}),
 *        @ORM\Index(name="fk_fee_user1_idx", columns={"waive_recommender_user_id"}),
 *        @ORM\Index(name="fk_fee_user2_idx", columns={"waive_approver_user_id"}),
 *        @ORM\Index(name="fk_fee_waive_reason1_idx", columns={"waive_reason_id"}),
 *        @ORM\Index(name="fk_fee_user3_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_fee_user4_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_fee_irfo_gv_permit1_idx", columns={"irfo_gv_permit_id"})
 *    }
 * )
 */
class Fee implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\IrfoGvPermitManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\LicenceManyToOneAlt1,
        Traits\TaskManyToOne,
        Traits\ApplicationManyToOneAlt1,
        Traits\ReceivedDateField,
        Traits\Description255FieldAlt1,
        Traits\IrfoFeeId10Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Waive reason2
     *
     * @var \Olcs\Db\Entity\WaiveReason
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\WaiveReason", fetch="LAZY")
     * @ORM\JoinColumn(name="waive_reason_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveReason2;

    /**
     * Waive approver user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="waive_approver_user_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveApproverUser;

    /**
     * Waive recommender user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="waive_recommender_user_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveRecommenderUser;

    /**
     * Parent fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Fee", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_fee_id", referencedColumnName="id", nullable=true)
     */
    protected $parentFee;

    /**
     * Fee type
     *
     * @var \Olcs\Db\Entity\FeeType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\FeeType", fetch="LAZY")
     * @ORM\JoinColumn(name="fee_type_id", referencedColumnName="id", nullable=false)
     */
    protected $feeType;

    /**
     * Amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="amount", precision=10, scale=2, nullable=false)
     */
    protected $amount;

    /**
     * Received amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="received_amount", precision=10, scale=2, nullable=true)
     */
    protected $receivedAmount;

    /**
     * Invoice no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="invoice_no", nullable=true)
     */
    protected $invoiceNo;

    /**
     * Invoice line no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="invoice_line_no", nullable=true)
     */
    protected $invoiceLineNo;

    /**
     * Invoiced date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="invoiced_date", nullable=true)
     */
    protected $invoicedDate;

    /**
     * Fee status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fee_status", length=20, nullable=true)
     */
    protected $feeStatus;

    /**
     * Waive approval date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="waive_approval_date", nullable=true)
     */
    protected $waiveApprovalDate;

    /**
     * Waive reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="waive_reason", length=255, nullable=true)
     */
    protected $waiveReason;

    /**
     * Waive recommendation date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="waive_recommendation_date", nullable=true)
     */
    protected $waiveRecommendationDate;

    /**
     * Irfo fee exempt
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="irfo_fee_exempt", nullable=true)
     */
    protected $irfoFeeExempt;

    /**
     * Irfo file no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_file_no", length=10, nullable=true)
     */
    protected $irfoFileNo;


    /**
     * Set the waive reason2
     *
     * @param \Olcs\Db\Entity\WaiveReason $waiveReason2
     * @return Fee
     */
    public function setWaiveReason2($waiveReason2)
    {
        $this->waiveReason2 = $waiveReason2;

        return $this;
    }

    /**
     * Get the waive reason2
     *
     * @return \Olcs\Db\Entity\WaiveReason
     */
    public function getWaiveReason2()
    {
        return $this->waiveReason2;
    }

    /**
     * Set the waive approver user
     *
     * @param \Olcs\Db\Entity\User $waiveApproverUser
     * @return Fee
     */
    public function setWaiveApproverUser($waiveApproverUser)
    {
        $this->waiveApproverUser = $waiveApproverUser;

        return $this;
    }

    /**
     * Get the waive approver user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getWaiveApproverUser()
    {
        return $this->waiveApproverUser;
    }

    /**
     * Set the waive recommender user
     *
     * @param \Olcs\Db\Entity\User $waiveRecommenderUser
     * @return Fee
     */
    public function setWaiveRecommenderUser($waiveRecommenderUser)
    {
        $this->waiveRecommenderUser = $waiveRecommenderUser;

        return $this;
    }

    /**
     * Get the waive recommender user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getWaiveRecommenderUser()
    {
        return $this->waiveRecommenderUser;
    }

    /**
     * Set the parent fee
     *
     * @param \Olcs\Db\Entity\Fee $parentFee
     * @return Fee
     */
    public function setParentFee($parentFee)
    {
        $this->parentFee = $parentFee;

        return $this;
    }

    /**
     * Get the parent fee
     *
     * @return \Olcs\Db\Entity\Fee
     */
    public function getParentFee()
    {
        return $this->parentFee;
    }

    /**
     * Set the fee type
     *
     * @param \Olcs\Db\Entity\FeeType $feeType
     * @return Fee
     */
    public function setFeeType($feeType)
    {
        $this->feeType = $feeType;

        return $this;
    }

    /**
     * Get the fee type
     *
     * @return \Olcs\Db\Entity\FeeType
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * Set the amount
     *
     * @param float $amount
     * @return Fee
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the received amount
     *
     * @param float $receivedAmount
     * @return Fee
     */
    public function setReceivedAmount($receivedAmount)
    {
        $this->receivedAmount = $receivedAmount;

        return $this;
    }

    /**
     * Get the received amount
     *
     * @return float
     */
    public function getReceivedAmount()
    {
        return $this->receivedAmount;
    }

    /**
     * Set the invoice no
     *
     * @param int $invoiceNo
     * @return Fee
     */
    public function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;

        return $this;
    }

    /**
     * Get the invoice no
     *
     * @return int
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * Set the invoice line no
     *
     * @param int $invoiceLineNo
     * @return Fee
     */
    public function setInvoiceLineNo($invoiceLineNo)
    {
        $this->invoiceLineNo = $invoiceLineNo;

        return $this;
    }

    /**
     * Get the invoice line no
     *
     * @return int
     */
    public function getInvoiceLineNo()
    {
        return $this->invoiceLineNo;
    }

    /**
     * Set the invoiced date
     *
     * @param \DateTime $invoicedDate
     * @return Fee
     */
    public function setInvoicedDate($invoicedDate)
    {
        $this->invoicedDate = $invoicedDate;

        return $this;
    }

    /**
     * Get the invoiced date
     *
     * @return \DateTime
     */
    public function getInvoicedDate()
    {
        return $this->invoicedDate;
    }

    /**
     * Set the fee status
     *
     * @param string $feeStatus
     * @return Fee
     */
    public function setFeeStatus($feeStatus)
    {
        $this->feeStatus = $feeStatus;

        return $this;
    }

    /**
     * Get the fee status
     *
     * @return string
     */
    public function getFeeStatus()
    {
        return $this->feeStatus;
    }

    /**
     * Set the waive approval date
     *
     * @param \DateTime $waiveApprovalDate
     * @return Fee
     */
    public function setWaiveApprovalDate($waiveApprovalDate)
    {
        $this->waiveApprovalDate = $waiveApprovalDate;

        return $this;
    }

    /**
     * Get the waive approval date
     *
     * @return \DateTime
     */
    public function getWaiveApprovalDate()
    {
        return $this->waiveApprovalDate;
    }

    /**
     * Set the waive reason
     *
     * @param string $waiveReason
     * @return Fee
     */
    public function setWaiveReason($waiveReason)
    {
        $this->waiveReason = $waiveReason;

        return $this;
    }

    /**
     * Get the waive reason
     *
     * @return string
     */
    public function getWaiveReason()
    {
        return $this->waiveReason;
    }

    /**
     * Set the waive recommendation date
     *
     * @param \DateTime $waiveRecommendationDate
     * @return Fee
     */
    public function setWaiveRecommendationDate($waiveRecommendationDate)
    {
        $this->waiveRecommendationDate = $waiveRecommendationDate;

        return $this;
    }

    /**
     * Get the waive recommendation date
     *
     * @return \DateTime
     */
    public function getWaiveRecommendationDate()
    {
        return $this->waiveRecommendationDate;
    }

    /**
     * Set the irfo fee exempt
     *
     * @param string $irfoFeeExempt
     * @return Fee
     */
    public function setIrfoFeeExempt($irfoFeeExempt)
    {
        $this->irfoFeeExempt = $irfoFeeExempt;

        return $this;
    }

    /**
     * Get the irfo fee exempt
     *
     * @return string
     */
    public function getIrfoFeeExempt()
    {
        return $this->irfoFeeExempt;
    }

    /**
     * Set the irfo file no
     *
     * @param string $irfoFileNo
     * @return Fee
     */
    public function setIrfoFileNo($irfoFileNo)
    {
        $this->irfoFileNo = $irfoFileNo;

        return $this;
    }

    /**
     * Get the irfo file no
     *
     * @return string
     */
    public function getIrfoFileNo()
    {
        return $this->irfoFileNo;
    }
}
