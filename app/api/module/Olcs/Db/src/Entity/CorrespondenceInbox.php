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
 *        @ORM\Index(name="IDX_C8A620F65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_C8A620FDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C8A620F26EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_C8A620FC33F7837", columns={"document_id"})
 *    }
 * )
 */
class CorrespondenceInbox implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
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
