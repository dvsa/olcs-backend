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
 *        @ORM\Index(name="ix_queue_status", columns={"status"})
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
     * Priority
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="priority", nullable=true, options={"default": 100})
     */
    protected $priority = 100;

    /**
     * Process after date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="process_after_date", nullable=true)
     */
    protected $processAfterDate;

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
     * Set the priority
     *
     * @param int $priority
     * @return Queue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
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
