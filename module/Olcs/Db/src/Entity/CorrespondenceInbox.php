<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CorrespondenceInbox Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
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
class CorrespondenceInbox implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DocumentManyToOne,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

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
}
