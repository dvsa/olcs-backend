<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicles declarations status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait VehiclesDeclarationsStatusField
{
    /**
     * Vehicles declarations status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_declarations_status", nullable=true)
     */
    protected $vehiclesDeclarationsStatus;

    /**
     * Set the vehicles declarations status
     *
     * @param int $vehiclesDeclarationsStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVehiclesDeclarationsStatus($vehiclesDeclarationsStatus)
    {
        $this->vehiclesDeclarationsStatus = $vehiclesDeclarationsStatus;

        return $this;
    }

    /**
     * Get the vehicles declarations status
     *
     * @return int
     */
    public function getVehiclesDeclarationsStatus()
    {
        return $this->vehiclesDeclarationsStatus;
    }
}
