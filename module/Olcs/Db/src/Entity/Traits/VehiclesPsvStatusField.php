<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicles psv status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait VehiclesPsvStatusField
{
    /**
     * Vehicles psv status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_psv_status", nullable=true)
     */
    protected $vehiclesPsvStatus;

    /**
     * Set the vehicles psv status
     *
     * @param int $vehiclesPsvStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVehiclesPsvStatus($vehiclesPsvStatus)
    {
        $this->vehiclesPsvStatus = $vehiclesPsvStatus;

        return $this;
    }

    /**
     * Get the vehicles psv status
     *
     * @return int
     */
    public function getVehiclesPsvStatus()
    {
        return $this->vehiclesPsvStatus;
    }
}
