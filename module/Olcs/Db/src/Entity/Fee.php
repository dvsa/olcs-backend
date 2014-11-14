<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fee Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee",
 *    indexes={
 *        @ORM\Index(name="fk_fee_application1_idx", 
 *            columns={"application_id"}),
 *        @ORM\Index(name="fk_fee_bus_reg1_idx", 
 *            columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_fee_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_fee_task1_idx", 
 *            columns={"task_id"}),
 *        @ORM\Index(name="fk_fee_fee_type1_idx", 
 *            columns={"fee_type_id"}),
 *        @ORM\Index(name="fk_fee_fee1_idx", 
 *            columns={"parent_fee_id"}),
 *        @ORM\Index(name="fk_fee_user1_idx", 
 *            columns={"waive_recommender_user_id"}),
 *        @ORM\Index(name="fk_fee_user2_idx", 
 *            columns={"waive_approver_user_id"}),
 *        @ORM\Index(name="fk_fee_waive_reason1_idx", 
 *            columns={"waive_reason_id"}),
 *        @ORM\Index(name="fk_fee_user3_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_fee_user4_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_fee_irfo_gv_permit1_idx", 
 *            columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="fk_fee_ref_data1_idx", 
 *            columns={"fee_status"}),
 *        @ORM\Index(name="fk_fee_ref_data2_idx", 
 *            columns={"payment_method"})
 *    }
 * )
 */
class Fee implements Interfaces\EntityInterface
{

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
     * Waive approver user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="waive_approver_user_id", referencedColumnName="id", nullable=true)
     */
    protected $waiveApproverUser;

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
     * Payment method
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="payment_method", referencedColumnName="id", nullable=true)
     */
    protected $paymentMethod;

    /**
     * Fee status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="fee_status", referencedColumnName="id", nullable=true)
     */
    protected $feeStatus;

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
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="received_date", nullable=true)
     */
    protected $receivedDate;

    /**
     * Receipt no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="receipt_no", length=45, nullable=true)
     */
    protected $receiptNo;

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
     * Task
     *
     * @var \Olcs\Db\Entity\Task
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Task", fetch="LAZY")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    protected $task;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg", fetch="LAZY")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

    /**
     * Irfo gv permit
     *
     * @var \Olcs\Db\Entity\IrfoGvPermit
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoGvPermit", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoGvPermit;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

    /**
     * Irfo fee id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_fee_id", length=10, nullable=true)
     */
    protected $irfoFeeId;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return Fee
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get the received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the task
     *
     * @param \Olcs\Db\Entity\Task $task
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get the task
     *
     * @return \Olcs\Db\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * Set the irfo gv permit
     *
     * @param \Olcs\Db\Entity\IrfoGvPermit $irfoGvPermit
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoGvPermit($irfoGvPermit)
    {
        $this->irfoGvPermit = $irfoGvPermit;

        return $this;
    }

    /**
     * Get the irfo gv permit
     *
     * @return \Olcs\Db\Entity\IrfoGvPermit
     */
    public function getIrfoGvPermit()
    {
        return $this->irfoGvPermit;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the irfo fee id
     *
     * @param string $irfoFeeId
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoFeeId($irfoFeeId)
    {
        $this->irfoFeeId = $irfoFeeId;

        return $this;
    }

    /**
     * Get the irfo fee id
     *
     * @return string
     */
    public function getIrfoFeeId()
    {
        return $this->irfoFeeId;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
