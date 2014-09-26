<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusRegOtherService Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg_other_service",
 *    indexes={
 *        @ORM\Index(name="IDX_3DECF1FC5327B2E3", columns={"bus_reg_id"}),
 *        @ORM\Index(name="IDX_3DECF1FCDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_3DECF1FC65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class BusRegOtherService implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ServiceNo70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg", fetch="LAZY", inversedBy="otherServices")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=false)
     */
    protected $busReg;

    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return BusRegOtherService
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
