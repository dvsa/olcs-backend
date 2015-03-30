<?php
/**
 * Bus Reg History View
 *
 * @NOTE: This walks and talks like an entity but be warned, it is backed
 * by a view. As such it is is nicely readable and searchable, but writes
 * are a no go.
 *
 * You'll notice that the entity has no setters; this is intentional to
 * try and prevent accidental writes. It's marked as readOnly too to
 * prevent doctrine including it in any flushes
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Olcs\Db\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Interfaces;

/**
 * Bus Reg History View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="bus_reg_history_view")
 */
class BusRegHistoryView implements Interfaces\EntityInterface
{
    /**
     * Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * eventDatetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="event_datetime")
     */
    protected $eventDatetime;

    /**
     * Event data
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_data", length=255, nullable=true)
     */
    protected $eventData;

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
     * User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getEventDatetime()
    {
        return $this->eventDatetime;
    }

    /**
     * @return string
     */
    public function getEventData()
    {
        return $this->eventData;
    }

    /**
     * @return \Olcs\Db\Entity\EventHistoryType
     */
    public function getEventHistoryType()
    {
        return $this->eventHistoryType;
    }

    /**
     * @return \Olcs\Db\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
