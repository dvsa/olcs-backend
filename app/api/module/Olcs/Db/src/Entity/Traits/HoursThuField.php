<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours thu field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursThuField
{
    /**
     * Hours thu
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="hours_thu", nullable=true)
     */
    protected $hoursThu;

    /**
     * Set the hours thu
     *
     * @param int $hoursThu
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursThu($hoursThu)
    {
        $this->hoursThu = $hoursThu;

        return $this;
    }

    /**
     * Get the hours thu
     *
     * @return int
     */
    public function getHoursThu()
    {
        return $this->hoursThu;
    }
}
