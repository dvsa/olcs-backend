<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hours tue field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HoursTueField
{
    /**
     * Hours tue
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_tue", nullable=true)
     */
    protected $hoursTue;

    /**
     * Set the hours tue
     *
     * @param int $hoursTue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHoursTue($hoursTue)
    {
        $this->hoursTue = $hoursTue;

        return $this;
    }

    /**
     * Get the hours tue
     *
     * @return int
     */
    public function getHoursTue()
    {
        return $this->hoursTue;
    }
}
