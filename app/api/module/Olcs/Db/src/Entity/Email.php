<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Email Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email",
 *    indexes={
 *        @ORM\Index(name="fk_email_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_email_user2_idx", columns={"last_updated_by"})
 *    }
 * )
 */
class Email implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\AddedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Last updated by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_updated_by", referencedColumnName="id", nullable=true)
     */
    protected $lastUpdatedBy;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Document", inversedBy="emails", fetch="LAZY")
     * @ORM\JoinTable(name="email_attachment",
     *     joinColumns={
     *         @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $documents;

    /**
     * Deferred date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deferred_date", nullable=true)
     */
    protected $deferredDate;

    /**
     * Sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="sent_date", nullable=true)
     */
    protected $sentDate;

    /**
     * Importance
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="importance", nullable=true)
     */
    protected $importance;

    /**
     * Is sensitive
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_sensitive", nullable=true)
     */
    protected $isSensitive;

    /**
     * Subject
     *
     * @var string
     *
     * @ORM\Column(type="string", name="subject", length=255, nullable=true)
     */
    protected $subject;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * Set the last updated by
     *
     * @param \Olcs\Db\Entity\User $lastUpdatedBy
     * @return Email
     */
    public function setLastUpdatedBy($lastUpdatedBy)
    {
        $this->lastUpdatedBy = $lastUpdatedBy;

        return $this;
    }

    /**
     * Get the last updated by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastUpdatedBy()
    {
        return $this->lastUpdatedBy;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Email
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
     * @return Email
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
     * @return Email
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }

    /**
     * Set the deferred date
     *
     * @param \DateTime $deferredDate
     * @return Email
     */
    public function setDeferredDate($deferredDate)
    {
        $this->deferredDate = $deferredDate;

        return $this;
    }

    /**
     * Get the deferred date
     *
     * @return \DateTime
     */
    public function getDeferredDate()
    {
        return $this->deferredDate;
    }

    /**
     * Set the sent date
     *
     * @param \DateTime $sentDate
     * @return Email
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * Get the sent date
     *
     * @return \DateTime
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set the importance
     *
     * @param int $importance
     * @return Email
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;

        return $this;
    }

    /**
     * Get the importance
     *
     * @return int
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * Set the is sensitive
     *
     * @param string $isSensitive
     * @return Email
     */
    public function setIsSensitive($isSensitive)
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    /**
     * Get the is sensitive
     *
     * @return string
     */
    public function getIsSensitive()
    {
        return $this->isSensitive;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     * @return Email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
