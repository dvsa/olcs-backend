<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicles status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait VehiclesStatusField
{
    /**
     * Vehicles status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_status", nullable=true)
     */
    protected $vehiclesStatus;

    /**
     * Set the vehicles status
     *
     * @param int $vehiclesStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVehiclesStatus($vehiclesStatus)
    {
        $this->vehiclesStatus = $vehiclesStatus;

        return $this;
    }

    /**
     * Get the vehicles status
     *
     * @return int
     */
    public function getVehiclesStatus()
    {
        return $this->vehiclesStatus;
    }
}
