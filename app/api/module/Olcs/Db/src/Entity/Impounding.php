<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Impounding Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="impounding",
 *    indexes={
 *        @ORM\Index(name="fk_impounding_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_impounding_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_impounding_transport_commissioner1_idx", 
 *            columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_impounding_ref_data1_idx", 
 *            columns={"outcome"}),
 *        @ORM\Index(name="fk_impounding_ref_data2_idx", 
 *            columns={"impounding_type"}),
 *        @ORM\Index(name="fk_impounding_cases1_idx", 
 *            columns={"case_id"}),
 *        @ORM\Index(name="fk_impounding_pi_venue1_idx", 
 *            columns={"pi_venue_id"})
 *    }
 * )
 */
class Impounding implements Interfaces\EntityInterface
{

    /**
     * Impounding type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="impounding_type", referencedColumnName="id", nullable=false)
     */
    protected $impoundingType;

    /**
     * Impounding legislation type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="impoundings", fetch="LAZY")
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
     * Application receipt date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="application_receipt_date", nullable=true)
     */
    protected $applicationReceiptDate;

    /**
     * Outcome sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="outcome_sent_date", nullable=true)
     */
    protected $outcomeSentDate;

    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="close_date", nullable=true)
     */
    protected $closeDate;

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
     * Presiding tc
     *
     * @var \Olcs\Db\Entity\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="presiding_tc_id", referencedColumnName="id", nullable=false)
     */
    protected $presidingTc;

    /**
     * Pi venue
     *
     * @var \Olcs\Db\Entity\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiVenue", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_venue_id", referencedColumnName="id", nullable=true)
     */
    protected $piVenue;

    /**
     * Outcome
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="outcome", referencedColumnName="id", nullable=true)
     */
    protected $outcome;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Hearing date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="hearing_date", nullable=true)
     */
    protected $hearingDate;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

    /**
     * Pi venue other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_venue_other", length=255, nullable=true)
     */
    protected $piVenueOther;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->impoundingLegislationTypes = new ArrayCollection();
    }

    /**
     * Set the impounding type
     *
     * @param \Olcs\Db\Entity\RefData $impoundingType
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getImpoundingType()
    {
        return $this->impoundingType;
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
     * Set the presiding tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $presidingTc
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPresidingTc($presidingTc)
    {
        $this->presidingTc = $presidingTc;

        return $this;
    }

    /**
     * Get the presiding tc
     *
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getPresidingTc()
    {
        return $this->presidingTc;
    }

    /**
     * Set the pi venue
     *
     * @param \Olcs\Db\Entity\PiVenue $piVenue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiVenue($piVenue)
    {
        $this->piVenue = $piVenue;

        return $this;
    }

    /**
     * Get the pi venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getPiVenue()
    {
        return $this->piVenue;
    }

    /**
     * Set the outcome
     *
     * @param \Olcs\Db\Entity\RefData $outcome
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the hearing date
     *
     * @param \DateTime $hearingDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the notes
     *
     * @param string $notes
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the pi venue other
     *
     * @param string $piVenueOther
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the vrm
     *
     * @param string $vrm
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
