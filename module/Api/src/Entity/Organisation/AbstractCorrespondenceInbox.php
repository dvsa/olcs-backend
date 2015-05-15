<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrespondenceInbox Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="correspondence_inbox",
 *    indexes={
 *        @ORM\Index(name="ix_correspondence_inbox_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_correspondence_inbox_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_correspondence_inbox_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_correspondence_inbox_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_correspondence_inbox_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractCorrespondenceInbox
{

    /**
     * Accessed
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="accessed", nullable=true, options={"default": 0})
     */
    protected $accessed = 0;

    /**
     * Archived
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="archived", nullable=true)
     */
    protected $archived;

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
     * Document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

    /**
     * Email reminder sent
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="email_reminder_sent", nullable=true)
     */
    protected $emailReminderSent;

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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Printed
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="printed", nullable=true)
     */
    protected $printed;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return CorrespondenceInbox
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
     * @return CorrespondenceInbox
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
     * Set the document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $document
     * @return CorrespondenceInbox
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getDocument()
    {
        return $this->document;
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
     * Set the id
     *
     * @param int $id
     * @return CorrespondenceInbox
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return CorrespondenceInbox
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
     * @return CorrespondenceInbox
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
     * @return CorrespondenceInbox
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return CorrespondenceInbox
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
     * Set the version
     *
     * @param int $version
     * @return CorrespondenceInbox
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
