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
 *        @ORM\Index(name="IDX_10CBCFC26639AAA5", columns={"opposer_id"}),
 *        @ORM\Index(name="IDX_10CBCFC2DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_10CBCFC265CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_10CBCFC23E030ACD", columns={"application_id"})
 *    }
 * )
 */
class Opposition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\IsRepresentationField,
        Traits\Notes4000Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Opposer
     *
     * @var \Olcs\Db\Entity\Opposer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposer", fetch="LAZY")
     * @ORM\JoinColumn(name="opposer_id", referencedColumnName="id", nullable=false)
     */
    protected $opposer;

    /**
     * Is copied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copied", nullable=false)
     */
    protected $isCopied;

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
    protected $isInTime;

    /**
     * Is public inquiry
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_public_inquiry", nullable=false)
     */
    protected $isPublicInquiry;

    /**
     * Is valid
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_valid", nullable=false)
     */
    protected $isValid;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
}
