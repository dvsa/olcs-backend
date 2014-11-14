<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Email Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email",
 *    indexes={
 *        @ORM\Index(name="fk_email_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_email_user2_idx", 
 *            columns={"last_updated_by"})
 *    }
 * )
 */
class Email implements Interfaces\EntityInterface
{

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
     * Added date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_date", nullable=true)
     */
    protected $addedDate;

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
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
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
