<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="ix_fee_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_fee_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_fee_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_fee_task_id", columns={"task_id"}),
 *        @ORM\Index(name="ix_fee_fee_type_id", columns={"fee_type_id"}),
 *        @ORM\Index(name="ix_fee_parent_fee_id", columns={"parent_fee_id"}),
 *        @ORM\Index(name="ix_fee_waive_recommender_user_id", columns={"waive_recommender_user_id"}),
 *        @ORM\Index(name="ix_fee_waive_approver_user_id", columns={"waive_approver_user_id"}),
 *        @ORM\Index(name="ix_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_fee_fee_status", columns={"fee_status"}),
 *        @ORM\Index(name="ix_fee_payment_method", columns={"payment_method"})
 *    }
 * )
 */
class Fee implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\IrfoFeeId10Field,
        Traits\IrfoGvPermitManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOneAlt1,
        Traits\ReceivedDateField,
        Traits\TaskManyToOne,
        Traits\CustomVersionField;

    /**
     * Amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="amount", precision=10, scale=2, nullable=false)
     */
    protected $amount;

    /**
     * Cheque po date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="cheque_po_date", nullable=true)
     */
    protected $chequePoDate;

    /**
     * Cheque po number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="cheque_po_number", length=100, nullable=true)
     */
    protected $chequePoNumber;

    /**
     * Fee status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="fee_status", referencedColumnName="id", nullable=false)
     */
    protected $feeStatus;

    /**
     * Fee type
     *
     * @var \Olcs\Db\Entity\FeeType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\FeeType")
     * @ORM\JoinColumn(name="fee_type_id", referencedColumnName="id", nullable=false)
     */
    protected $feeType;

    /**
     * Invoice line no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="invoice_line_no", nullable=true)
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
     * Parent fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Fee")
     * @ORM\JoinColumn(name="parent_fee_id", referencedColumnName="id", nullable=true)
     */
    protected $parentFee;

    /**
     * Payer name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="payer_name", length=100, nullable=true)
     */
    protected $payerName;

    /**
     * Paying in slip number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="paying_in_slip_number", length=100, nullable=true)
     */
    protected $payingInSlipNumber;

    /**
     * Payment method
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="payment_method", referencedColumnName="id", nullable=true)
     */
    protected $paymentMethod;

    /**
     * Receipt no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="receipt_no", length=45, nullable=true)
     */
    protected $receiptNo;

    /**
     * Received amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="received_amount", precision=10, scale=2, nullable=true)
     */
    protected $receivedAmount;

    /**
     * Waive approval date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="waive_approval_date", nullable=true)
     */
    protected $waiveApprovalDate;

    /**
     * Waive approver user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="waive_approver_user_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveApproverUser;

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
     * Waive recommender user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="waive_recommender_user_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveRecommenderUser;

    /**
     * Fee payment
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\FeePayment", mappedBy="fee")
     */
    protected $feePayments;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->feePayments = new ArrayCollection();
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
     * Set the cheque po date
     *
     * @param \DateTime $chequePoDate
     * @return Fee
     */
    public function setChequePoDate($chequePoDate)
    {
        $this->chequePoDate = $chequePoDate;

        return $this;
    }

    /**
     * Get the cheque po date
     *
     * @return \DateTime
     */
    public function getChequePoDate()
    {
        return $this->chequePoDate;
    }

    /**
     * Set the cheque po number
     *
     * @param string $chequePoNumber
     * @return Fee
     */
    public function setChequePoNumber($chequePoNumber)
    {
        $this->chequePoNumber = $chequePoNumber;

        return $this;
    }

    /**
     * Get the cheque po number
     *
     * @return string
     */
    public function getChequePoNumber()
    {
        return $this->chequePoNumber;
    }

    /**
     * Set the fee status
     *
     * @param \Olcs\Db\Entity\RefData $feeStatus
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getFeeStatus()
    {
        return $this->feeStatus;
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
     * Set the payer name
     *
     * @param string $payerName
     * @return Fee
     */
    public function setPayerName($payerName)
    {
        $this->payerName = $payerName;

        return $this;
    }

    /**
     * Get the payer name
     *
     * @return string
     */
    public function getPayerName()
    {
        return $this->payerName;
    }

    /**
     * Set the paying in slip number
     *
     * @param string $payingInSlipNumber
     * @return Fee
     */
    public function setPayingInSlipNumber($payingInSlipNumber)
    {
        $this->payingInSlipNumber = $payingInSlipNumber;

        return $this;
    }

    /**
     * Get the paying in slip number
     *
     * @return string
     */
    public function getPayingInSlipNumber()
    {
        return $this->payingInSlipNumber;
    }

    /**
     * Set the payment method
     *
     * @param \Olcs\Db\Entity\RefData $paymentMethod
     * @return Fee
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get the payment method
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set the receipt no
     *
     * @param string $receiptNo
     * @return Fee
     */
    public function setReceiptNo($receiptNo)
    {
        $this->receiptNo = $receiptNo;

        return $this;
    }

    /**
     * Get the receipt no
     *
     * @return string
     */
    public function getReceiptNo()
    {
        return $this->receiptNo;
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
     * Set the fee payment
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feePayments
     * @return Fee
     */
    public function setFeePayments($feePayments)
    {
        $this->feePayments = $feePayments;

        return $this;
    }

    /**
     * Get the fee payments
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFeePayments()
    {
        return $this->feePayments;
    }

    /**
     * Add a fee payments
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feePayments
     * @return Fee
     */
    public function addFeePayments($feePayments)
    {
        if ($feePayments instanceof ArrayCollection) {
            $this->feePayments = new ArrayCollection(
                array_merge(
                    $this->feePayments->toArray(),
                    $feePayments->toArray()
                )
            );
        } elseif (!$this->feePayments->contains($feePayments)) {
            $this->feePayments->add($feePayments);
        }

        return $this;
    }

    /**
     * Remove a fee payments
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feePayments
     * @return Fee
     */
    public function removeFeePayments($feePayments)
    {
        if ($this->feePayments->contains($feePayments)) {
            $this->feePayments->removeElement($feePayments);
        }

        return $this;
    }
}
