<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours mon field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursMonField
{
    /**
     * Hours mon
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_mon", nullable=true)
     */
    protected $hoursMon;

    /**
     * Set the hours mon
     *
     * @param int $hoursMon
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursMon($hoursMon)
    {
        $this->hoursMon = $hoursMon;

        return $this;
    }

    /**
     * Get the hours mon
     *
     * @return int
     */
    public function getHoursMon()
    {
        return $this->hoursMon;
    }
}
