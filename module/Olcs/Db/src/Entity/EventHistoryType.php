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
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=50, nullable=false)
     */
    protected $description;

    /**
     * Event code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_code", length=3, nullable=false)
     */
    protected $eventCode;

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
     * Set the event code
     *
     * @param string $eventCode
     * @return EventHistoryType
     */
    public function setEventCode($eventCode)
    {
        $this->eventCode = $eventCode;

        return $this;
    }

    /**
     * Get the event code
     *
     * @return string
     */
    public function getEventCode()
    {
        return $this->eventCode;
    }
}
