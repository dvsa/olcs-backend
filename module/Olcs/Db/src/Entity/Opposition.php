<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Opposition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="opposition",
 *    indexes={
 *        @ORM\Index(name="fk_opposition_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_opposition_opposer1_idx", columns={"opposer_id"}),
 *        @ORM\Index(name="fk_opposition_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_opposition_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_opposition_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_opposition_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_opposition_ref_data1_idx", columns={"opposition_type"}),
 *        @ORM\Index(name="fk_opposition_ref_data2_idx", columns={"status"})
 *    }
 * )
 */
class Opposition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\Notes4000Field,
        Traits\StatusManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="oppositions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="oppositions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

    /**
     * Ground
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="oppositions")
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
     * Is copied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copied", nullable=false)
     */
    protected $isCopied = 0;

    /**
     * Is in time
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_in_time", nullable=false)
     */
    protected $isInTime = 0;

    /**
     * Is public inquiry
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_public_inquiry", nullable=false)
     */
    protected $isPublicInquiry = 0;

    /**
     * Is valid
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_valid", nullable=false)
     */
    protected $isValid = 0;

    /**
     * Is willing to attend pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_willing_to_attend_pi", nullable=false)
     */
    protected $isWillingToAttendPi = 0;

    /**
     * Is withdrawn
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_withdrawn", nullable=false)
     */
    protected $isWithdrawn = 0;

    /**
     * Opposer
     *
     * @var \Olcs\Db\Entity\Opposer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposer", cascade={"persist"})
     * @ORM\JoinColumn(name="opposer_id", referencedColumnName="id", nullable=false)
     */
    protected $opposer;

    /**
     * Opposition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
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
     * Valid notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="valid_notes", length=4000, nullable=true)
     */
    protected $validNotes;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="opposition")
     */
    protected $documents;

    /**
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OperatingCentreOpposition", mappedBy="opposition")
     */
    protected $operatingCentres;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->grounds = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->operatingCentres = new ArrayCollection();
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
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
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
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
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
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
     * @param string $isValid
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
     * @return string
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
     * Set the opposer
     *
     * @param \Olcs\Db\Entity\Opposer $opposer
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
     * @return \Olcs\Db\Entity\Opposer
     */
    public function getOpposer()
    {
        return $this->opposer;
    }

    /**
     * Set the opposition type
     *
     * @param \Olcs\Db\Entity\RefData $oppositionType
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
     * @return \Olcs\Db\Entity\RefData
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
}
