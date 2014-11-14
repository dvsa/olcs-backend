<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Witnesses field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait WitnessesField
{
    /**
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witnesses", nullable=false)
     */
    protected $witnesses = 0;

    /**
     * Set the witnesses
     *
     * @param int $witnesses
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWitnesses($witnesses)
    {
        $this->witnesses = $witnesses;

        return $this;
    }

    /**
     * Get the witnesses
     *
     * @return int
     */
    public function getWitnesses()
    {
        return $this->witnesses;
    }
}
