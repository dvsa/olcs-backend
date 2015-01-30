<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours per week field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursPerWeekField
{
    /**
     * Hours per week
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_per_week", nullable=true)
     */
    protected $hoursPerWeek;

    /**
     * Set the hours per week
     *
     * @param int $hoursPerWeek
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursPerWeek($hoursPerWeek)
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    /**
     * Get the hours per week
     *
     * @return int
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }
}
