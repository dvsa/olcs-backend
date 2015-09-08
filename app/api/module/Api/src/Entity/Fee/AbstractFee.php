<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Fee Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee",
 *    indexes={
 *        @ORM\Index(name="ix_fee_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_fee_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_fee_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_fee_task_id", columns={"task_id"}),
 *        @ORM\Index(name="ix_fee_fee_type_id", columns={"fee_type_id"}),
 *        @ORM\Index(name="ix_fee_parent_fee_id", columns={"parent_fee_id"}),
 *        @ORM\Index(name="ix_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_fee_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_fee_fee_status", columns={"fee_status"})
 *    }
 * )
 */
abstract class AbstractFee implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="amount", precision=10, scale=2, nullable=false)
     */
    protected $amount;

    /**
     * Application
     *
     * @var \Dvsa\Olcs\Api\Entity\Application\Application
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Application\Application",
     *     fetch="LAZY",
     *     inversedBy="fees"
     * )
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Bus reg
     *
     * @var \Dvsa\Olcs\Api\Entity\Bus\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Bus\BusReg", fetch="LAZY")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

    /**
     * Fee status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="fee_status", referencedColumnName="id", nullable=false)
     */
    protected $feeStatus;

    /**
     * Fee type
     *
     * @var \Dvsa\Olcs\Api\Entity\Fee\FeeType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Fee\FeeType", fetch="LAZY")
     * @ORM\JoinColumn(name="fee_type_id", referencedColumnName="id", nullable=false)
     */
    protected $feeType;

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
     * Invoice line no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="invoice_line_no", nullable=false, options={"default": 1})
     */
    protected $invoiceLineNo = 1;

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
     * Irfo fee id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_fee_id", length=10, nullable=true)
     */
    protected $irfoFeeId;

    /**
     * Irfo file no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_file_no", length=10, nullable=true)
     */
    protected $irfoFileNo;

    /**
     * Irfo gv permit
     *
     * @var \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoGvPermit;

    /**
     * Irfo psv auth
     *
     * @var \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoPsvAuth;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence",
     *     fetch="LAZY",
     *     inversedBy="fees"
     * )
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Parent fee
     *
     * @var \Dvsa\Olcs\Api\Entity\Fee\Fee
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Fee\Fee", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_fee_id", referencedColumnName="id", nullable=true)
     */
    protected $parentFee;

    /**
     * Task
     *
     * @var \Dvsa\Olcs\Api\Entity\Task\Task
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Task\Task", fetch="LAZY")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    protected $task;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Fee transaction
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Fee\FeeTransaction",
     *     mappedBy="fee",
     *     cascade={"persist"}
     * )
     */
    protected $feeTransactions;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->feeTransactions = new ArrayCollection();
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
     * Set the application
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\Application $application
     * @return Fee
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Dvsa\Olcs\Api\Entity\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the bus reg
     *
     * @param \Dvsa\Olcs\Api\Entity\Bus\BusReg $busReg
     * @return Fee
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Dvsa\Olcs\Api\Entity\Bus\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Fee
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
     * @return Fee
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
     * Set the description
     *
     * @param string $description
     * @return Fee
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
     * Set the fee status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $feeStatus
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getFeeStatus()
    {
        return $this->feeStatus;
    }

    /**
     * Set the fee type
     *
     * @param \Dvsa\Olcs\Api\Entity\Fee\FeeType $feeType
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
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Fee
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
     * Set the irfo fee id
     *
     * @param string $irfoFeeId
     * @return Fee
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
     * Set the irfo gv permit
     *
     * @param \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit $irfoGvPermit
     * @return Fee
     */
    public function setIrfoGvPermit($irfoGvPermit)
    {
        $this->irfoGvPermit = $irfoGvPermit;

        return $this;
    }

    /**
     * Get the irfo gv permit
     *
     * @return \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit
     */
    public function getIrfoGvPermit()
    {
        return $this->irfoGvPermit;
    }

    /**
     * Set the irfo psv auth
     *
     * @param \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth $irfoPsvAuth
     * @return Fee
     */
    public function setIrfoPsvAuth($irfoPsvAuth)
    {
        $this->irfoPsvAuth = $irfoPsvAuth;

        return $this;
    }

    /**
     * Get the irfo psv auth
     *
     * @return \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth
     */
    public function getIrfoPsvAuth()
    {
        return $this->irfoPsvAuth;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Fee
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
     * @return Fee
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence
     * @return Fee
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the parent fee
     *
     * @param \Dvsa\Olcs\Api\Entity\Fee\Fee $parentFee
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
     * @return \Dvsa\Olcs\Api\Entity\Fee\Fee
     */
    public function getParentFee()
    {
        return $this->parentFee;
    }

    /**
     * Set the task
     *
     * @param \Dvsa\Olcs\Api\Entity\Task\Task $task
     * @return Fee
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get the task
     *
     * @return \Dvsa\Olcs\Api\Entity\Task\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return Fee
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
     * Set the fee transaction
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feeTransactions
     * @return Fee
     */
    public function setFeeTransactions($feeTransactions)
    {
        $this->feeTransactions = $feeTransactions;

        return $this;
    }

    /**
     * Get the fee transactions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFeeTransactions()
    {
        return $this->feeTransactions;
    }

    /**
     * Add a fee transactions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feeTransactions
     * @return Fee
     */
    public function addFeeTransactions($feeTransactions)
    {
        if ($feeTransactions instanceof ArrayCollection) {
            $this->feeTransactions = new ArrayCollection(
                array_merge(
                    $this->feeTransactions->toArray(),
                    $feeTransactions->toArray()
                )
            );
        } elseif (!$this->feeTransactions->contains($feeTransactions)) {
            $this->feeTransactions->add($feeTransactions);
        }

        return $this;
    }

    /**
     * Remove a fee transactions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $feeTransactions
     * @return Fee
     */
    public function removeFeeTransactions($feeTransactions)
    {
        if ($this->feeTransactions->contains($feeTransactions)) {
            $this->feeTransactions->removeElement($feeTransactions);
        }

        return $this;
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
