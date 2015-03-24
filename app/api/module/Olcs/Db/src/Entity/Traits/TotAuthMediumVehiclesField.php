<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tot auth medium vehicles field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TotAuthMediumVehiclesField
{
    /**
     * Tot auth medium vehicles
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="tot_auth_medium_vehicles", nullable=true)
     */
    protected $totAuthMediumVehicles;

    /**
     * Set the tot auth medium vehicles
     *
     * @param int $totAuthMediumVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthMediumVehicles($totAuthMediumVehicles)
    {
        $this->totAuthMediumVehicles = $totAuthMediumVehicles;

        return $this;
    }

    /**
     * Get the tot auth medium vehicles
     *
     * @return int
     */
    public function getTotAuthMediumVehicles()
    {
        return $this->totAuthMediumVehicles;
    }
}
