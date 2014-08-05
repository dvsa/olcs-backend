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
        Traits\LicenceManyToOne,
        Traits\DocumentManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Archived
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="archived", nullable=true)
     */
    protected $archived;

    /**
     * Accessed
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="accessed", nullable=true)
     */
    protected $accessed;

    /**
     * Email reminder sent
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="email_reminder_sent", nullable=true)
     */
    protected $emailReminderSent;

    /**
     * Printed
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="printed", nullable=true)
     */
    protected $printed;

    /**
     * Set the archived
     *
     * @param boolean $archived
     * @return \Olcs\Db\Entity\CorrespondenceInbox
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get the archived
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set the accessed
     *
     * @param boolean $accessed
     * @return \Olcs\Db\Entity\CorrespondenceInbox
     */
    public function setAccessed($accessed)
    {
        $this->accessed = $accessed;

        return $this;
    }

    /**
     * Get the accessed
     *
     * @return boolean
     */
    public function getAccessed()
    {
        return $this->accessed;
    }

    /**
     * Set the email reminder sent
     *
     * @param boolean $emailReminderSent
     * @return \Olcs\Db\Entity\CorrespondenceInbox
     */
    public function setEmailReminderSent($emailReminderSent)
    {
        $this->emailReminderSent = $emailReminderSent;

        return $this;
    }

    /**
     * Get the email reminder sent
     *
     * @return boolean
     */
    public function getEmailReminderSent()
    {
        return $this->emailReminderSent;
    }

    /**
     * Set the printed
     *
     * @param boolean $printed
     * @return \Olcs\Db\Entity\CorrespondenceInbox
     */
    public function setPrinted($printed)
    {
        $this->printed = $printed;

        return $this;
    }

    /**
     * Get the printed
     *
     * @return boolean
     */
    public function getPrinted()
    {
        return $this->printed;
    }
}
