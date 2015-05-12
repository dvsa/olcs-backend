<?php

namespace Dvsa\Olcs\Api\Entity\Opposition;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Opposition Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="opposition",
 *    indexes={
 *        @ORM\Index(name="ix_opposition_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_opposition_opposer_id", columns={"opposer_id"}),
 *        @ORM\Index(name="ix_opposition_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_opposition_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_opposition_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_opposition_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_opposition_opposition_type", columns={"opposition_type"}),
 *        @ORM\Index(name="ix_opposition_is_valid", columns={"is_valid"}),
 *        @ORM\Index(name="ix_opposition_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="ux_olbs_key", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
abstract class AbstractOpposition
{

    /**
     * Application
     *
     * @var \Dvsa\Olcs\Api\Entity\Application\Application
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Application\Application", inversedBy="oppositions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases", inversedBy="oppositions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

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
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Ground
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", inversedBy="oppositions")
     * @ORM\JoinTable(name="opposition_grounds",
     *     joinColumns={
     *         @ORM\JoinColumn(name="opposition_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="ground_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $grounds;

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
     * Is copied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copied", nullable=false, options={"default": 0})
     */
    protected $isCopied = 0;

    /**
     * Is in time
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_in_time", nullable=false, options={"default": 0})
     */
    protected $isInTime = 0;

    /**
     * Is public inquiry
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_public_inquiry", nullable=false, options={"default": 0})
     */
    protected $isPublicInquiry = 0;

    /**
     * Is valid
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="is_valid", referencedColumnName="id", nullable=false)
     */
    protected $isValid;

    /**
     * Is willing to attend pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_willing_to_attend_pi", nullable=false, options={"default": 0})
     */
    protected $isWillingToAttendPi = 0;

    /**
     * Is withdrawn
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_withdrawn", nullable=false, options={"default": 0})
     */
    protected $isWithdrawn = 0;

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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

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
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre", inversedBy="oppositions")
     * @ORM\JoinTable(name="operating_centre_opposition",
     *     joinColumns={
     *         @ORM\JoinColumn(name="opposition_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $operatingCentres;

    /**
     * Opposer
     *
     * @var \Dvsa\Olcs\Api\Entity\Opposition\Opposer
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Opposition\Opposer", cascade={"persist"})
     * @ORM\JoinColumn(name="opposer_id", referencedColumnName="id", nullable=false)
     */
    protected $opposer;

    /**
     * Opposition type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="opposition_type", referencedColumnName="id", nullable=false)
     */
    protected $oppositionType;

    /**
     * Raised date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="raised_date", nullable=true)
     */
    protected $raisedDate;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Valid notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="valid_notes", length=4000, nullable=true)
     */
    protected $validNotes;

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
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", mappedBy="opposition")
     */
    protected $documents;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->grounds = new ArrayCollection();
        $this->operatingCentres = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * Set the application
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\Application $application
     * @return Opposition
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
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @return Opposition
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Opposition
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
     * @return Opposition
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
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return Opposition
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the ground
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $grounds
     * @return Opposition
     */
    public function setGrounds($grounds)
    {
        $this->grounds = $grounds;

        return $this;
    }

    /**
     * Get the grounds
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGrounds()
    {
        return $this->grounds;
    }

    /**
     * Add a grounds
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $grounds
     * @return Opposition
     */
    public function addGrounds($grounds)
    {
        if ($grounds instanceof ArrayCollection) {
            $this->grounds = new ArrayCollection(
                array_merge(
                    $this->grounds->toArray(),
                    $grounds->toArray()
                )
            );
        } elseif (!$this->grounds->contains($grounds)) {
            $this->grounds->add($grounds);
        }

        return $this;
    }

    /**
     * Remove a grounds
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $grounds
     * @return Opposition
     */
    public function removeGrounds($grounds)
    {
        if ($this->grounds->contains($grounds)) {
            $this->grounds->removeElement($grounds);
        }

        return $this;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Opposition
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
     * Set the is copied
     *
     * @param string $isCopied
     * @return Opposition
     */
    public function setIsCopied($isCopied)
    {
        $this->isCopied = $isCopied;

        return $this;
    }

    /**
     * Get the is copied
     *
     * @return string
     */
    public function getIsCopied()
    {
        return $this->isCopied;
    }

    /**
     * Set the is in time
     *
     * @param string $isInTime
     * @return Opposition
     */
    public function setIsInTime($isInTime)
    {
        $this->isInTime = $isInTime;

        return $this;
    }

    /**
     * Get the is in time
     *
     * @return string
     */
    public function getIsInTime()
    {
        return $this->isInTime;
    }

    /**
     * Set the is public inquiry
     *
     * @param string $isPublicInquiry
     * @return Opposition
     */
    public function setIsPublicInquiry($isPublicInquiry)
    {
        $this->isPublicInquiry = $isPublicInquiry;

        return $this;
    }

    /**
     * Get the is public inquiry
     *
     * @return string
     */
    public function getIsPublicInquiry()
    {
        return $this->isPublicInquiry;
    }

    /**
     * Set the is valid
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $isValid
     * @return Opposition
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get the is valid
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set the is willing to attend pi
     *
     * @param string $isWillingToAttendPi
     * @return Opposition
     */
    public function setIsWillingToAttendPi($isWillingToAttendPi)
    {
        $this->isWillingToAttendPi = $isWillingToAttendPi;

        return $this;
    }

    /**
     * Get the is willing to attend pi
     *
     * @return string
     */
    public function getIsWillingToAttendPi()
    {
        return $this->isWillingToAttendPi;
    }

    /**
     * Set the is withdrawn
     *
     * @param string $isWithdrawn
     * @return Opposition
     */
    public function setIsWithdrawn($isWithdrawn)
    {
        $this->isWithdrawn = $isWithdrawn;

        return $this;
    }

    /**
     * Get the is withdrawn
     *
     * @return string
     */
    public function getIsWithdrawn()
    {
        return $this->isWithdrawn;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Opposition
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
     * @return Opposition
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
     * @return Opposition
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
     * Set the notes
     *
     * @param string $notes
     * @return Opposition
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return Opposition
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
     * @return Opposition
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
     * Set the operating centre
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Opposition
     */
    public function setOperatingCentres($operatingCentres)
    {
        $this->operatingCentres = $operatingCentres;

        return $this;
    }

    /**
     * Get the operating centres
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOperatingCentres()
    {
        return $this->operatingCentres;
    }

    /**
     * Add a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Opposition
     */
    public function addOperatingCentres($operatingCentres)
    {
        if ($operatingCentres instanceof ArrayCollection) {
            $this->operatingCentres = new ArrayCollection(
                array_merge(
                    $this->operatingCentres->toArray(),
                    $operatingCentres->toArray()
                )
            );
        } elseif (!$this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->add($operatingCentres);
        }

        return $this;
    }

    /**
     * Remove a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Opposition
     */
    public function removeOperatingCentres($operatingCentres)
    {
        if ($this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->removeElement($operatingCentres);
        }

        return $this;
    }

    /**
     * Set the opposer
     *
     * @param \Dvsa\Olcs\Api\Entity\Opposition\Opposer $opposer
     * @return Opposition
     */
    public function setOpposer($opposer)
    {
        $this->opposer = $opposer;

        return $this;
    }

    /**
     * Get the opposer
     *
     * @return \Dvsa\Olcs\Api\Entity\Opposition\Opposer
     */
    public function getOpposer()
    {
        return $this->opposer;
    }

    /**
     * Set the opposition type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $oppositionType
     * @return Opposition
     */
    public function setOppositionType($oppositionType)
    {
        $this->oppositionType = $oppositionType;

        return $this;
    }

    /**
     * Get the opposition type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getOppositionType()
    {
        return $this->oppositionType;
    }

    /**
     * Set the raised date
     *
     * @param \DateTime $raisedDate
     * @return Opposition
     */
    public function setRaisedDate($raisedDate)
    {
        $this->raisedDate = $raisedDate;

        return $this;
    }

    /**
     * Get the raised date
     *
     * @return \DateTime
     */
    public function getRaisedDate()
    {
        return $this->raisedDate;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status
     * @return Opposition
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the valid notes
     *
     * @param string $validNotes
     * @return Opposition
     */
    public function setValidNotes($validNotes)
    {
        $this->validNotes = $validNotes;

        return $this;
    }

    /**
     * Get the valid notes
     *
     * @return string
     */
    public function getValidNotes()
    {
        return $this->validNotes;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return Opposition
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
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Opposition
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Opposition
     */
    public function addDocuments($documents)
    {
        if ($documents instanceof ArrayCollection) {
            $this->documents = new ArrayCollection(
                array_merge(
                    $this->documents->toArray(),
                    $documents->toArray()
                )
            );
        } elseif (!$this->documents->contains($documents)) {
            $this->documents->add($documents);
        }

        return $this;
    }

    /**
     * Remove a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Opposition
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
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
}
