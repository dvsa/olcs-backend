<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventHistory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="event_history",
 *    indexes={
 *        @ORM\Index(name="fk_event_history_event_history_type1_idx", 
 *            columns={"event_history_type_id"}),
 *        @ORM\Index(name="fk_event_history_user1_idx", 
 *            columns={"user_id"}),
 *        @ORM\Index(name="fk_event_history_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_event_history_application1_idx", 
 *            columns={"application_id"}),
 *        @ORM\Index(name="fk_event_history_licence_vehicle1_idx", 
 *            columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="fk_event_history_team1_idx", 
 *            columns={"team_id"}),
 *        @ORM\Index(name="fk_event_history_transport_manager1_idx", 
 *            columns={"transport_manager_id"})
 *    }
 * )
 */
class EventHistory implements Interfaces\EntityInterface
{

    /**
     * Team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     */
    protected $team;

    /**
     * Licence vehicle
     *
     * @var \Olcs\Db\Entity\LicenceVehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LicenceVehicle", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_vehicle_id", referencedColumnName="id", nullable=true)
     */
    protected $licenceVehicle;

    /**
     * Event history type
     *
     * @var \Olcs\Db\Entity\EventHistoryType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EventHistoryType", fetch="LAZY")
     * @ORM\JoinColumn(name="event_history_type_id", referencedColumnName="id", nullable=false)
     */
    protected $eventHistoryType;

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
     * Entity type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="entity_type", length=45, nullable=true)
     */
    protected $entityType;

    /**
     * Entity pk
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_pk", nullable=true)
     */
    protected $entityPk;

    /**
     * Entity version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_version", nullable=true)
     */
    protected $entityVersion;

    /**
     * Entity data
     *
     * @var string
     *
     * @ORM\Column(type="text", name="entity_data", length=65535, nullable=true)
     */
    protected $entityData;

    /**
     * Operation
     *
     * @var string
     *
     * @ORM\Column(type="string", name="operation", length=1, nullable=true)
     */
    protected $operation;

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
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", fetch="LAZY")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

    /**
     * User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Set the team
     *
     * @param \Olcs\Db\Entity\Team $team
     * @return EventHistory
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Olcs\Db\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Olcs\Db\Entity\LicenceVehicle $licenceVehicle
     * @return EventHistory
     */
    public function setLicenceVehicle($licenceVehicle)
    {
        $this->licenceVehicle = $licenceVehicle;

        return $this;
    }

    /**
     * Get the licence vehicle
     *
     * @return \Olcs\Db\Entity\LicenceVehicle
     */
    public function getLicenceVehicle()
    {
        return $this->licenceVehicle;
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
     * Set the entity data
     *
     * @param string $entityData
     * @return EventHistory
     */
    public function setEntityData($entityData)
    {
        $this->entityData = $entityData;

        return $this;
    }

    /**
     * Get the entity data
     *
     * @return string
     */
    public function getEntityData()
    {
        return $this->entityData;
    }

    /**
     * Set the operation
     *
     * @param string $operation
     * @return EventHistory
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get the operation
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the user
     *
     * @param \Olcs\Db\Entity\User $user
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}
