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
 *        @ORM\Index(name="fk_correspondence_inbox_document1_idx", columns={"document_id"}),
 *        @ORM\Index(name="fk_correspondence_inbox_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_correspondence_inbox_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_correspondence_inbox_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CorrespondenceInbox implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOneAlt1,
        Traits\DocumentManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
}
