<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoGvPermit Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_gv_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_gv_permit_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_irfo_gv_permit_type1_idx", 
 *            columns={"irfo_gv_permit_type_id"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_ref_data1_idx", 
 *            columns={"irfo_permit_status"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_ref_data2_idx", 
 *            columns={"withdrawn_reason"})
 *    }
 * )
 */
class IrfoGvPermit implements Interfaces\EntityInterface
{

    /**
     * Irfo permit status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_permit_status", referencedColumnName="id", nullable=false)
     */
    protected $irfoPermitStatus;

    /**
     * Irfo gv permit type
     *
     * @var \Olcs\Db\Entity\IrfoGvPermitType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoGvPermitType", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoGvPermitType;

    /**
     * Is fee exempt
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt", nullable=false)
     */
    protected $isFeeExempt = 0;

    /**
     * No of copies
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_copies", nullable=false)
     */
    protected $noOfCopies = 0;

    /**
     * Note
     *
     * @var string
     *
     * @ORM\Column(type="string", name="note", length=2000, nullable=true)
     */
    protected $note;

    /**
     * Permit printed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permit_printed", nullable=false)
     */
    protected $permitPrinted = 0;

    /**
     * Year required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="year_required", nullable=true)
     */
    protected $yearRequired;

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
     * Withdrawn reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawnReason;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Exemption details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="exemption_details", length=255, nullable=true)
     */
    protected $exemptionDetails;

    /**
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="expiry_date", nullable=true)
     */
    protected $expiryDate;

    /**
     * In force date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="in_force_date", nullable=true)
     */
    protected $inForceDate;

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
     * Set the irfo permit status
     *
     * @param \Olcs\Db\Entity\RefData $irfoPermitStatus
     * @return IrfoGvPermit
     */
    public function setIrfoPermitStatus($irfoPermitStatus)
    {
        $this->irfoPermitStatus = $irfoPermitStatus;

        return $this;
    }

    /**
     * Get the irfo permit status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getIrfoPermitStatus()
    {
        return $this->irfoPermitStatus;
    }

    /**
     * Set the irfo gv permit type
     *
     * @param \Olcs\Db\Entity\IrfoGvPermitType $irfoGvPermitType
     * @return IrfoGvPermit
     */
    public function setIrfoGvPermitType($irfoGvPermitType)
    {
        $this->irfoGvPermitType = $irfoGvPermitType;

        return $this;
    }

    /**
     * Get the irfo gv permit type
     *
     * @return \Olcs\Db\Entity\IrfoGvPermitType
     */
    public function getIrfoGvPermitType()
    {
        return $this->irfoGvPermitType;
    }

    /**
     * Set the is fee exempt
     *
     * @param string $isFeeExempt
     * @return IrfoGvPermit
     */
    public function setIsFeeExempt($isFeeExempt)
    {
        $this->isFeeExempt = $isFeeExempt;

        return $this;
    }

    /**
     * Get the is fee exempt
     *
     * @return string
     */
    public function getIsFeeExempt()
    {
        return $this->isFeeExempt;
    }

    /**
     * Set the no of copies
     *
     * @param int $noOfCopies
     * @return IrfoGvPermit
     */
    public function setNoOfCopies($noOfCopies)
    {
        $this->noOfCopies = $noOfCopies;

        return $this;
    }

    /**
     * Get the no of copies
     *
     * @return int
     */
    public function getNoOfCopies()
    {
        return $this->noOfCopies;
    }

    /**
     * Set the note
     *
     * @param string $note
     * @return IrfoGvPermit
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the permit printed
     *
     * @param string $permitPrinted
     * @return IrfoGvPermit
     */
    public function setPermitPrinted($permitPrinted)
    {
        $this->permitPrinted = $permitPrinted;

        return $this;
    }

    /**
     * Get the permit printed
     *
     * @return string
     */
    public function getPermitPrinted()
    {
        return $this->permitPrinted;
    }

    /**
     * Set the year required
     *
     * @param int $yearRequired
     * @return IrfoGvPermit
     */
    public function setYearRequired($yearRequired)
    {
        $this->yearRequired = $yearRequired;

        return $this;
    }

    /**
     * Get the year required
     *
     * @return int
     */
    public function getYearRequired()
    {
        return $this->yearRequired;
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
     * Set the withdrawn reason
     *
     * @param \Olcs\Db\Entity\RefData $withdrawnReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExemptionDetails($exemptionDetails)
    {
        $this->exemptionDetails = $exemptionDetails;

        return $this;
    }

    /**
     * Get the exemption details
     *
     * @return string
     */
    public function getExemptionDetails()
    {
        return $this->exemptionDetails;
    }

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set the in force date
     *
     * @param \DateTime $inForceDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setInForceDate($inForceDate)
    {
        $this->inForceDate = $inForceDate;

        return $this;
    }

    /**
     * Get the in force date
     *
     * @return \DateTime
     */
    public function getInForceDate()
    {
        return $this->inForceDate;
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
