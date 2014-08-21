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
 *        @ORM\Index(name="fk_event_history_event_history_type1_idx", columns={"event_history_type_id"}),
 *        @ORM\Index(name="fk_event_history_user1_idx", columns={"user_id"}),
 *        @ORM\Index(name="fk_event_history_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_event_history_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_event_history_licence_vehicle1_idx", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="fk_event_history_team1_idx", columns={"team_id"})
 *    }
 * )
 */
class EventHistory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOneAlt1,
        Traits\LicenceManyToOneAlt1,
        Traits\UserManyToOne;

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
     * Team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     */
    protected $team;

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
}
