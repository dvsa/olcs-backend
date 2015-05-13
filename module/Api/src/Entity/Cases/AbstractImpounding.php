<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Impounding Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="impounding",
 *    indexes={
 *        @ORM\Index(name="ix_impounding_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_impounding_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_impounding_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_impounding_outcome", columns={"outcome"}),
 *        @ORM\Index(name="ix_impounding_impounding_type", columns={"impounding_type"}),
 *        @ORM\Index(name="ix_impounding_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_impounding_pi_venue_id", columns={"pi_venue_id"})
 *    }
 * )
 */
abstract class AbstractImpounding
{

    /**
     * Application receipt date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="application_receipt_date", nullable=true)
     */
    protected $applicationReceiptDate;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="close_date", nullable=true)
     */
    protected $closeDate;

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
     * Impounding legislation type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", inversedBy="impoundings")
     * @ORM\JoinTable(name="impounding_legislation_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="impounding_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="impounding_legislation_type_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $impoundingLegislationTypes;

    /**
     * Impounding type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="impounding_type", referencedColumnName="id", nullable=false)
     */
    protected $impoundingType;

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
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

    /**
     * Outcome
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="outcome", referencedColumnName="id", nullable=true)
     */
    protected $outcome;

    /**
     * Outcome sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="outcome_sent_date", nullable=true)
     */
    protected $outcomeSentDate;

    /**
     * Pi venue
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PiVenue")
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
     * Presiding tc
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PresidingTc")
     * @ORM\JoinColumn(name="presiding_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $presidingTc;

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
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->impoundingLegislationTypes = new ArrayCollection();
    }

    /**
     * Set the application receipt date
     *
     * @param \DateTime $applicationReceiptDate
     * @return Impounding
     */
    public function setApplicationReceiptDate($applicationReceiptDate)
    {
        $this->applicationReceiptDate = $applicationReceiptDate;

        return $this;
    }

    /**
     * Get the application receipt date
     *
     * @return \DateTime
     */
    public function getApplicationReceiptDate()
    {
        return $this->applicationReceiptDate;
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @return Impounding
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the close date
     *
     * @param \DateTime $closeDate
     * @return Impounding
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get the close date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Impounding
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
     * @return Impounding
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
     * Set the hearing date
     *
     * @param \DateTime $hearingDate
     * @return Impounding
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
     * @return Impounding
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
     * Set the impounding legislation type
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function setImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        $this->impoundingLegislationTypes = $impoundingLegislationTypes;

        return $this;
    }

    /**
     * Get the impounding legislation types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getImpoundingLegislationTypes()
    {
        return $this->impoundingLegislationTypes;
    }

    /**
     * Add a impounding legislation types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function addImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        if ($impoundingLegislationTypes instanceof ArrayCollection) {
            $this->impoundingLegislationTypes = new ArrayCollection(
                array_merge(
                    $this->impoundingLegislationTypes->toArray(),
                    $impoundingLegislationTypes->toArray()
                )
            );
        } elseif (!$this->impoundingLegislationTypes->contains($impoundingLegislationTypes)) {
            $this->impoundingLegislationTypes->add($impoundingLegislationTypes);
        }

        return $this;
    }

    /**
     * Remove a impounding legislation types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function removeImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        if ($this->impoundingLegislationTypes->contains($impoundingLegislationTypes)) {
            $this->impoundingLegislationTypes->removeElement($impoundingLegislationTypes);
        }

        return $this;
    }

    /**
     * Set the impounding type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $impoundingType
     * @return Impounding
     */
    public function setImpoundingType($impoundingType)
    {
        $this->impoundingType = $impoundingType;

        return $this;
    }

    /**
     * Get the impounding type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getImpoundingType()
    {
        return $this->impoundingType;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Impounding
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
     * @return Impounding
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
     * Set the notes
     *
     * @param string $notes
     * @return Impounding
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the outcome
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $outcome
     * @return Impounding
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * Set the outcome sent date
     *
     * @param \DateTime $outcomeSentDate
     * @return Impounding
     */
    public function setOutcomeSentDate($outcomeSentDate)
    {
        $this->outcomeSentDate = $outcomeSentDate;

        return $this;
    }

    /**
     * Get the outcome sent date
     *
     * @return \DateTime
     */
    public function getOutcomeSentDate()
    {
        return $this->outcomeSentDate;
    }

    /**
     * Set the pi venue
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PiVenue $piVenue
     * @return Impounding
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
     * @return Impounding
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
     * Set the presiding tc
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PresidingTc $presidingTc
     * @return Impounding
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
     * Set the version
     *
     * @param int $version
     * @return Impounding
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
     * Set the vrm
     *
     * @param string $vrm
     * @return Impounding
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
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
