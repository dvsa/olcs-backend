<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taxi phv status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TaxiPhvStatusField
{
    /**
     * Taxi phv status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="taxi_phv_status", nullable=true)
     */
    protected $taxiPhvStatus;

    /**
     * Set the taxi phv status
     *
     * @param int $taxiPhvStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTaxiPhvStatus($taxiPhvStatus)
    {
        $this->taxiPhvStatus = $taxiPhvStatus;

        return $this;
    }

    /**
     * Get the taxi phv status
     *
     * @return int
     */
    public function getTaxiPhvStatus()
    {
        return $this->taxiPhvStatus;
    }
}
