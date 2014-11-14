<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrespondenceInbox Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="correspondence_inbox",
 *    indexes={
 *        @ORM\Index(name="fk_correspondence_inbox_document1_idx", 
 *            columns={"document_id"}),
 *        @ORM\Index(name="fk_correspondence_inbox_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_correspondence_inbox_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_correspondence_inbox_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class CorrespondenceInbox implements Interfaces\EntityInterface
{

    /**
     * Archived
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="archived", nullable=true)
     */
    protected $archived;

    /**
     * Accessed
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="accessed", nullable=true)
     */
    protected $accessed;

    /**
     * Email reminder sent
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="email_reminder_sent", nullable=true)
     */
    protected $emailReminderSent;

    /**
     * Printed
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="printed", nullable=true)
     */
    protected $printed;

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
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

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
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

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
     * Set the archived
     *
     * @param string $archived
     * @return CorrespondenceInbox
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get the archived
     *
     * @return string
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set the accessed
     *
     * @param string $accessed
     * @return CorrespondenceInbox
     */
    public function setAccessed($accessed)
    {
        $this->accessed = $accessed;

        return $this;
    }

    /**
     * Get the accessed
     *
     * @return string
     */
    public function getAccessed()
    {
        return $this->accessed;
    }

    /**
     * Set the email reminder sent
     *
     * @param string $emailReminderSent
     * @return CorrespondenceInbox
     */
    public function setEmailReminderSent($emailReminderSent)
    {
        $this->emailReminderSent = $emailReminderSent;

        return $this;
    }

    /**
     * Get the email reminder sent
     *
     * @return string
     */
    public function getEmailReminderSent()
    {
        return $this->emailReminderSent;
    }

    /**
     * Set the printed
     *
     * @param string $printed
     * @return CorrespondenceInbox
     */
    public function setPrinted($printed)
    {
        $this->printed = $printed;

        return $this;
    }

    /**
     * Get the printed
     *
     * @return string
     */
    public function getPrinted()
    {
        return $this->printed;
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
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the document
     *
     * @param \Olcs\Db\Entity\Document $document
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
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
