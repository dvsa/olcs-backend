<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tot auth large vehicles field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TotAuthLargeVehiclesField
{
    /**
     * Tot auth large vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_large_vehicles", nullable=true)
     */
    protected $totAuthLargeVehicles;

    /**
     * Set the tot auth large vehicles
     *
     * @param int $totAuthLargeVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthLargeVehicles($totAuthLargeVehicles)
    {
        $this->totAuthLargeVehicles = $totAuthLargeVehicles;

        return $this;
    }

    /**
     * Get the tot auth large vehicles
     *
     * @return int
     */
    public function getTotAuthLargeVehicles()
    {
        return $this->totAuthLargeVehicles;
    }
}
