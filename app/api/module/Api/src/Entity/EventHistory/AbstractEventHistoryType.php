<?php

namespace Dvsa\Olcs\Api\Entity\EventHistory;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * EventHistoryType Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="event_history_type",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_event_history_type_event_code", columns={"event_code"})
 *    }
 * )
 */
abstract class AbstractEventHistoryType implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * @ORM\Column(type="string", name="event_code", length=3, nullable=true)
     */
    protected $eventCode;

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
     * Set the description
     *
     * @param string $description new value being set
     *
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
     * @param string $eventCode new value being set
     *
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

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
