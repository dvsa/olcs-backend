<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EventHistoryType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="event_history_type")
 */
class EventHistoryType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=50, nullable=false)
     */
    protected $description;

    /**
     * Event type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_type", length=3, nullable=false)
     */
    protected $eventType;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=3)
     */
    protected $id;

    /**
     * Set the description
     *
     * @param string $description
     * @return EventHistoryType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the event type
     *
     * @param string $eventType
     * @return EventHistoryType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * Get the event type
     *
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Set the id
     *
     * @param string $id
     * @return EventHistoryType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
