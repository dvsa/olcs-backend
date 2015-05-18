<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Queue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="queue",
 *    indexes={
 *        @ORM\Index(name="ix_queue_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_queue_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_queue_type", columns={"type"}),
 *        @ORM\Index(name="ix_queue_status", columns={"status"}),
 *        @ORM\Index(name="ix_queue_next", columns={"status","created_on","process_after_date"})
 *    }
 * )
 */
class Queue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\StatusManyToOne,
        Traits\TypeManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Attempts
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="attempts", nullable=false, options={"default": 0})
     */
    protected $attempts = 0;

    /**
     * Entity id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_id", nullable=true)
     */
    protected $entityId;

    /**
     * Options
     *
     * @var string
     *
     * @ORM\Column(type="string", name="options", length=4000, nullable=false, options={"default": ""})
     */
    protected $options = '';

    /**
     * Process after date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="process_after_date", nullable=true)
     */
    protected $processAfterDate;

    /**
     * Set the attempts
     *
     * @param int $attempts
     * @return Queue
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get the attempts
     *
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Set the entity id
     *
     * @param int $entityId
     * @return Queue
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get the entity id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set the options
     *
     * @param string $options
     * @return Queue
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the process after date
     *
     * @param \DateTime $processAfterDate
     * @return Queue
     */
    public function setProcessAfterDate($processAfterDate)
    {
        $this->processAfterDate = $processAfterDate;

        return $this;
    }

    /**
     * Get the process after date
     *
     * @return \DateTime
     */
    public function getProcessAfterDate()
    {
        return $this->processAfterDate;
    }
}
