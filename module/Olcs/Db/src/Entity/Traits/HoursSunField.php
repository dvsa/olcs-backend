<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours sun field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursSunField
{
    /**
     * Hours sun
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_sun", nullable=true)
     */
    protected $hoursSun;

    /**
     * Set the hours sun
     *
     * @param int $hoursSun
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursSun($hoursSun)
    {
        $this->hoursSun = $hoursSun;

        return $this;
    }

    /**
     * Get the hours sun
     *
     * @return int
     */
    public function getHoursSun()
    {
        return $this->hoursSun;
    }
}
