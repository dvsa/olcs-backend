<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * No of vehicles required field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NoOfVehiclesRequiredField
{
    /**
     * No of vehicles required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_vehicles_required", nullable=true)
     */
    protected $noOfVehiclesRequired;

    /**
     * Set the no of vehicles required
     *
     * @param int $noOfVehiclesRequired
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfVehiclesRequired($noOfVehiclesRequired)
    {
        $this->noOfVehiclesRequired = $noOfVehiclesRequired;

        return $this;
    }

    /**
     * Get the no of vehicles required
     *
     * @return int
     */
    public function getNoOfVehiclesRequired()
    {
        return $this->noOfVehiclesRequired;
    }

}
