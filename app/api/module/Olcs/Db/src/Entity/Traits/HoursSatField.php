<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours sat field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursSatField
{
    /**
     * Hours sat
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="hours_sat", nullable=true)
     */
    protected $hoursSat;

    /**
     * Set the hours sat
     *
     * @param int $hoursSat
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursSat($hoursSat)
    {
        $this->hoursSat = $hoursSat;

        return $this;
    }

    /**
     * Get the hours sat
     *
     * @return int
     */
    public function getHoursSat()
    {
        return $this->hoursSat;
    }
}
