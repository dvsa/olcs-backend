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
 *        @ORM\Index(name="fk_opposition_ref_data1", columns={"opposition_type"})
 *    }
 * )
 */
class Opposition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Notes4000Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Opposer
     *
     * @var \Olcs\Db\Entity\Opposer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposer")
     * @ORM\JoinColumn(name="opposer_id", referencedColumnName="id", nullable=false)
     */
    protected $opposer;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="oppositions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Is copied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copied", nullable=false)
     */
    protected $isCopied = 0;

    /**
     * Raised date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="raised_date", nullable=true)
     */
    protected $raisedDate;

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
    protected $isValid;

    /**
     * Is withdrawn
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_withdrawn", nullable=false)
     */
    protected $isWithdrawn = 0;

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
     * Ground
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OppositionGrounds", mappedBy="opposition")
     */
    protected $grounds;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->grounds = new ArrayCollection();
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
}
