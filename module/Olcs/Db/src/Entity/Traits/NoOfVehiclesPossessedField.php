<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * No of vehicles possessed field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NoOfVehiclesPossessedField
{
    /**
     * No of vehicles possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_vehicles_possessed", nullable=true)
     */
    protected $noOfVehiclesPossessed;

    /**
     * Set the no of vehicles possessed
     *
     * @param int $noOfVehiclesPossessed
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfVehiclesPossessed($noOfVehiclesPossessed)
    {
        $this->noOfVehiclesPossessed = $noOfVehiclesPossessed;

        return $this;
    }

    /**
     * Get the no of vehicles possessed
     *
     * @return int
     */
    public function getNoOfVehiclesPossessed()
    {
        return $this->noOfVehiclesPossessed;
    }
}
