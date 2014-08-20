<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reschedule datetime field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait RescheduleDatetimeField
{
    /**
     * Reschedule datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="reschedule_datetime", nullable=true)
     */
    protected $rescheduleDatetime;

    /**
     * Set the reschedule datetime
     *
     * @param \DateTime $rescheduleDatetime
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRescheduleDatetime($rescheduleDatetime)
    {
        $this->rescheduleDatetime = $rescheduleDatetime;

        return $this;
    }

    /**
     * Get the reschedule datetime
     *
     * @return \DateTime
     */
    public function getRescheduleDatetime()
    {
        return $this->rescheduleDatetime;
    }
}
