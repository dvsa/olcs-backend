<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoGvPermit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_gv_permit",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_gv_permit_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_irfo_gv_permit_type_id", columns={"irfo_gv_permit_type_id"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_irfo_permit_status", columns={"irfo_permit_status"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_withdrawn_reason", columns={"withdrawn_reason"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_gv_permit_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractIrfoGvPermit
{

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
     * Irfo gv permit type
     *
     * @var \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoGvPermitType;

    /**
     * Irfo permit status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_permit_status", referencedColumnName="id", nullable=false)
     */
    protected $irfoPermitStatus;

    /**
     * Is fee exempt
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt", nullable=false, options={"default": 0})
     */
    protected $isFeeExempt = 0;

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
     * No of copies
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_copies", nullable=false, options={"default": 0})
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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Organisation
     *
     * @var \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Organisation\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Permit printed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permit_printed", nullable=false, options={"default": 0})
     */
    protected $permitPrinted = 0;

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
     * Withdrawn reason
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawnReason;

    /**
     * Year required
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="year_required", nullable=true)
     */
    protected $yearRequired;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return IrfoGvPermit
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
     * @return IrfoGvPermit
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
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return IrfoGvPermit
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
     * @return IrfoGvPermit
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
     * Set the id
     *
     * @param int $id
     * @return IrfoGvPermit
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
     * Set the in force date
     *
     * @param \DateTime $inForceDate
     * @return IrfoGvPermit
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
     * @return IrfoGvPermit
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
     * Set the irfo gv permit type
     *
     * @param \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType $irfoGvPermitType
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
     * @return \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType
     */
    public function getIrfoGvPermitType()
    {
        return $this->irfoGvPermitType;
    }

    /**
     * Set the irfo permit status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $irfoPermitStatus
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getIrfoPermitStatus()
    {
        return $this->irfoPermitStatus;
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return IrfoGvPermit
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
     * @return IrfoGvPermit
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return IrfoGvPermit
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the organisation
     *
     * @param \Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     * @return IrfoGvPermit
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
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
     * Set the version
     *
     * @param int $version
     * @return IrfoGvPermit
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
     * Set the withdrawn reason
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $withdrawnReason
     * @return IrfoGvPermit
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
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
