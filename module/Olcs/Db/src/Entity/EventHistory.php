<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EventHistory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="event_history",
 *    indexes={
 *        @ORM\Index(name="ix_event_history_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_event_history_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_event_history_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_event_history_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_event_history_event_history_type1_idx", columns={"event_history_type_id"}),
 *        @ORM\Index(name="fk_event_history_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_event_history_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_event_history_bus_reg1_idx", columns={"bus_reg_id"})
 *    }
 * )
 */
class EventHistory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\CaseManyToOne,
        Traits\IdIdentity,
        Traits\LicenceManyToOneAlt1,
        Traits\OrganisationManyToOneAlt1,
        Traits\TransportManagerManyToOne,
        Traits\UserManyToOne;

    /**
     * Entity pk
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_pk", nullable=true)
     */
    protected $entityPk;

    /**
     * Entity type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="entity_type", length=45, nullable=true)
     */
    protected $entityType;

    /**
     * Entity version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_version", nullable=true)
     */
    protected $entityVersion;

    /**
     * Event data
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_data", length=255, nullable=true)
     */
    protected $eventData;

    /**
     * Event datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="event_datetime", nullable=false)
     */
    protected $eventDatetime;

    /**
     * Event description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_description", length=255, nullable=true)
     */
    protected $eventDescription;

    /**
     * Event history type
     *
     * @var \Olcs\Db\Entity\EventHistoryType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EventHistoryType")
     * @ORM\JoinColumn(name="event_history_type_id", referencedColumnName="id", nullable=false)
     */
    protected $eventHistoryType;

    /**
     * Set the entity pk
     *
     * @param int $entityPk
     * @return EventHistory
     */
    public function setEntityPk($entityPk)
    {
        $this->entityPk = $entityPk;

        return $this;
    }

    /**
     * Get the entity pk
     *
     * @return int
     */
    public function getEntityPk()
    {
        return $this->entityPk;
    }

    /**
     * Set the entity type
     *
     * @param string $entityType
     * @return EventHistory
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Get the entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Set the entity version
     *
     * @param int $entityVersion
     * @return EventHistory
     */
    public function setEntityVersion($entityVersion)
    {
        $this->entityVersion = $entityVersion;

        return $this;
    }

    /**
     * Get the entity version
     *
     * @return int
     */
    public function getEntityVersion()
    {
        return $this->entityVersion;
    }

    /**
     * Set the event data
     *
     * @param string $eventData
     * @return EventHistory
     */
    public function setEventData($eventData)
    {
        $this->eventData = $eventData;

        return $this;
    }

    /**
     * Get the event data
     *
     * @return string
     */
    public function getEventData()
    {
        return $this->eventData;
    }

    /**
     * Set the event datetime
     *
     * @param \DateTime $eventDatetime
     * @return EventHistory
     */
    public function setEventDatetime($eventDatetime)
    {
        $this->eventDatetime = $eventDatetime;

        return $this;
    }

    /**
     * Get the event datetime
     *
     * @return \DateTime
     */
    public function getEventDatetime()
    {
        return $this->eventDatetime;
    }

    /**
     * Set the event description
     *
     * @param string $eventDescription
     * @return EventHistory
     */
    public function setEventDescription($eventDescription)
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    /**
     * Get the event description
     *
     * @return string
     */
    public function getEventDescription()
    {
        return $this->eventDescription;
    }

    /**
     * Set the event history type
     *
     * @param \Olcs\Db\Entity\EventHistoryType $eventHistoryType
     * @return EventHistory
     */
    public function setEventHistoryType($eventHistoryType)
    {
        $this->eventHistoryType = $eventHistoryType;

        return $this;
    }

    /**
     * Get the event history type
     *
     * @return \Olcs\Db\Entity\EventHistoryType
     */
    public function getEventHistoryType()
    {
        return $this->eventHistoryType;
    }
}
