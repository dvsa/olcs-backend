<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours wed field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursWedField
{
    /**
     * Hours wed
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="hours_wed", nullable=true)
     */
    protected $hoursWed;

    /**
     * Set the hours wed
     *
     * @param int $hoursWed
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursWed($hoursWed)
    {
        $this->hoursWed = $hoursWed;

        return $this;
    }

    /**
     * Get the hours wed
     *
     * @return int
     */
    public function getHoursWed()
    {
        return $this->hoursWed;
    }
}
