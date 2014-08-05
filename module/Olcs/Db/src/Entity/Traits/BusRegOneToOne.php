<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bus reg one to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait BusRegOneToOne
{
    /**
     * Identifier - Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\BusReg")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     */
    protected $busReg;

    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }
}
