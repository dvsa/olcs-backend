<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tot auth small vehicles field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TotAuthSmallVehiclesField
{
    /**
     * Tot auth small vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_small_vehicles", nullable=true)
     */
    protected $totAuthSmallVehicles;

    /**
     * Set the tot auth small vehicles
     *
     * @param int $totAuthSmallVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthSmallVehicles($totAuthSmallVehicles)
    {
        $this->totAuthSmallVehicles = $totAuthSmallVehicles;

        return $this;
    }

    /**
     * Get the tot auth small vehicles
     *
     * @return int
     */
    public function getTotAuthSmallVehicles()
    {
        return $this->totAuthSmallVehicles;
    }

}
