<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours per week80 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursPerWeek80Field
{
    /**
     * Hours per week
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hours_per_week", length=80, nullable=true)
     */
    protected $hoursPerWeek;

    /**
     * Set the hours per week
     *
     * @param string $hoursPerWeek
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
     * @return string
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }
}
