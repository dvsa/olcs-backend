<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiHearing Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_hearing",
 *    indexes={
 *        @ORM\Index(name="ix_pi_hearing_pi_id", columns={"pi_id"}),
 *        @ORM\Index(name="ix_pi_hearing_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_pi_hearing_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_hearing_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_pi_hearing_presided_by_role", columns={"presided_by_role"}),
 *        @ORM\Index(name="ix_pi_hearing_pi_venue_id", columns={"pi_venue_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_pi_hearing_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
abstract class AbstractPiHearing
{

    /**
     * Adjourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="adjourned_date", nullable=true)
     */
    protected $adjournedDate;

    /**
     * Adjourned reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="adjourned_reason", length=4000, nullable=true)
     */
    protected $adjournedReason;

    /**
     * Cancelled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancelled_date", nullable=true)
     */
    protected $cancelledDate;

    /**
     * Cancelled reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="cancelled_reason", length=4000, nullable=true)
     */
    protected $cancelledReason;

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
     * Details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details", length=4000, nullable=true)
     */
    protected $details;

    /**
     * Hearing date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="hearing_date", nullable=true)
     */
    protected $hearingDate;

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
     * Is adjourned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false, options={"default": 0})
     */
    protected $isAdjourned = 0;

    /**
     * Is cancelled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_cancelled", nullable=false, options={"default": 0})
     */
    protected $isCancelled = 0;

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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=32, nullable=true)
     */
    protected $olbsType;

    /**
     * Pi
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\Pi
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\Pi", fetch="LAZY", inversedBy="piHearings")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Pi venue
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PiVenue", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_venue_id", referencedColumnName="id", nullable=true)
     */
    protected $piVenue;

    /**
     * Pi venue other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_venue_other", length=255, nullable=true)
     */
    protected $piVenueOther;

    /**
     * Presided by role
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by_role", referencedColumnName="id", nullable=true)
     */
    protected $presidedByRole;

    /**
     * Presiding tc
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="presiding_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $presidingTc;

    /**
     * Presiding tc other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="presiding_tc_other", length=45, nullable=true)
     */
    protected $presidingTcOther;

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
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="witnesses", nullable=true)
     */
    protected $witnesses;

    /**
     * Set the adjourned date
     *
     * @param \DateTime $adjournedDate
     * @return PiHearing
     */
    public function setAdjournedDate($adjournedDate)
    {
        $this->adjournedDate = $adjournedDate;

        return $this;
    }

    /**
     * Get the adjourned date
     *
     * @return \DateTime
     */
    public function getAdjournedDate()
    {
        return $this->adjournedDate;
    }

    /**
     * Set the adjourned reason
     *
     * @param string $adjournedReason
     * @return PiHearing
     */
    public function setAdjournedReason($adjournedReason)
    {
        $this->adjournedReason = $adjournedReason;

        return $this;
    }

    /**
     * Get the adjourned reason
     *
     * @return string
     */
    public function getAdjournedReason()
    {
        return $this->adjournedReason;
    }

    /**
     * Set the cancelled date
     *
     * @param \DateTime $cancelledDate
     * @return PiHearing
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get the cancelled date
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Set the cancelled reason
     *
     * @param string $cancelledReason
     * @return PiHearing
     */
    public function setCancelledReason($cancelledReason)
    {
        $this->cancelledReason = $cancelledReason;

        return $this;
    }

    /**
     * Get the cancelled reason
     *
     * @return string
     */
    public function getCancelledReason()
    {
        return $this->cancelledReason;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return PiHearing
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
     * @return PiHearing
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
     * Set the details
     *
     * @param string $details
     * @return PiHearing
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the hearing date
     *
     * @param \DateTime $hearingDate
     * @return PiHearing
     */
    public function setHearingDate($hearingDate)
    {
        $this->hearingDate = $hearingDate;

        return $this;
    }

    /**
     * Get the hearing date
     *
     * @return \DateTime
     */
    public function getHearingDate()
    {
        return $this->hearingDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return PiHearing
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
     * Set the is adjourned
     *
     * @param string $isAdjourned
     * @return PiHearing
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return string
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
    }

    /**
     * Set the is cancelled
     *
     * @param string $isCancelled
     * @return PiHearing
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get the is cancelled
     *
     * @return string
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return PiHearing
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
     * @return PiHearing
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return PiHearing
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
     * Set the olbs type
     *
     * @param string $olbsType
     * @return PiHearing
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
    }

    /**
     * Set the pi
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\Pi $pi
     * @return PiHearing
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Dvsa\Olcs\Api\Entity\Pi\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * Set the pi venue
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PiVenue $piVenue
     * @return PiHearing
     */
    public function setPiVenue($piVenue)
    {
        $this->piVenue = $piVenue;

        return $this;
    }

    /**
     * Get the pi venue
     *
     * @return \Dvsa\Olcs\Api\Entity\Pi\PiVenue
     */
    public function getPiVenue()
    {
        return $this->piVenue;
    }

    /**
     * Set the pi venue other
     *
     * @param string $piVenueOther
     * @return PiHearing
     */
    public function setPiVenueOther($piVenueOther)
    {
        $this->piVenueOther = $piVenueOther;

        return $this;
    }

    /**
     * Get the pi venue other
     *
     * @return string
     */
    public function getPiVenueOther()
    {
        return $this->piVenueOther;
    }

    /**
     * Set the presided by role
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $presidedByRole
     * @return PiHearing
     */
    public function setPresidedByRole($presidedByRole)
    {
        $this->presidedByRole = $presidedByRole;

        return $this;
    }

    /**
     * Get the presided by role
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getPresidedByRole()
    {
        return $this->presidedByRole;
    }

    /**
     * Set the presiding tc
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PresidingTc $presidingTc
     * @return PiHearing
     */
    public function setPresidingTc($presidingTc)
    {
        $this->presidingTc = $presidingTc;

        return $this;
    }

    /**
     * Get the presiding tc
     *
     * @return \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     */
    public function getPresidingTc()
    {
        return $this->presidingTc;
    }

    /**
     * Set the presiding tc other
     *
     * @param string $presidingTcOther
     * @return PiHearing
     */
    public function setPresidingTcOther($presidingTcOther)
    {
        $this->presidingTcOther = $presidingTcOther;

        return $this;
    }

    /**
     * Get the presiding tc other
     *
     * @return string
     */
    public function getPresidingTcOther()
    {
        return $this->presidingTcOther;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return PiHearing
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
     * Set the witnesses
     *
     * @param int $witnesses
     * @return PiHearing
     */
    public function setWitnesses($witnesses)
    {
        $this->witnesses = $witnesses;

        return $this;
    }

    /**
     * Get the witnesses
     *
     * @return int
     */
    public function getWitnesses()
    {
        return $this->witnesses;
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
