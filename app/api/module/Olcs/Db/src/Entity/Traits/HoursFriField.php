<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours fri field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursFriField
{
    /**
     * Hours fri
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_fri", nullable=true)
     */
    protected $hoursFri;

    /**
     * Set the hours fri
     *
     * @param int $hoursFri
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursFri($hoursFri)
    {
        $this->hoursFri = $hoursFri;

        return $this;
    }

    /**
     * Get the hours fri
     *
     * @return int
     */
    public function getHoursFri()
    {
        return $this->hoursFri;
    }
}
