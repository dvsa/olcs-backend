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
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="last_updated_by", referencedColumnName="id")
     */
    protected $lastUpdatedBy;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Document", inversedBy="emails")
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
     * @var boolean
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
     * @return \Olcs\Db\Entity\Email
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

     * @return \Olcs\Db\Entity\Email
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set the deferred date
     *
     * @param \DateTime $deferredDate
     * @return \Olcs\Db\Entity\Email
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
     * @return \Olcs\Db\Entity\Email
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
     * @return \Olcs\Db\Entity\Email
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
     * @param boolean $isSensitive
     * @return \Olcs\Db\Entity\Email
     */
    public function setIsSensitive($isSensitive)
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    /**
     * Get the is sensitive
     *
     * @return boolean
     */
    public function getIsSensitive()
    {
        return $this->isSensitive;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     * @return \Olcs\Db\Entity\Email
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
