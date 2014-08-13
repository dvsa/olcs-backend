<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * BusServiceType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_service_type")
 */
class BusServiceType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Description70Field;

    /**
     * Bus reg
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusReg", inversedBy="busServiceTypes")
     * @ORM\JoinTable(name="bus_reg_bus_service_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="bus_service_type_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $busRegs;

    /**
     * Txc service type name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_service_type_name", length=70, nullable=true)
     */
    protected $txcServiceTypeName;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->busRegs = new ArrayCollection();
    }


    /**
     * Set the bus reg
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return BusServiceType
     */
    public function setBusRegs($busRegs)
    {
        $this->busRegs = $busRegs;

        return $this;
    }

    /**
     * Get the bus regs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBusRegs()
    {
        return $this->busRegs;
    }

    /**
     * Add a bus regs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return BusServiceType
     */
    public function addBusRegs($busRegs)
    {
        if ($busRegs instanceof ArrayCollection) {
            $this->busRegs = new ArrayCollection(
                array_merge(
                    $this->busRegs->toArray(),
                    $busRegs->toArray()
                )
            );
        } elseif (!$this->busRegs->contains($busRegs)) {
            $this->busRegs->add($busRegs);
        }

        return $this;
    }

    /**
     * Remove a bus regs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return BusServiceType
     */
    public function removeBusRegs($busRegs)
    {
        if ($this->busRegs->contains($busRegs)) {
            $this->busRegs->remove($busRegs);
        }

        return $this;
    }


    /**
     * Set the txc service type name
     *
     * @param string $txcServiceTypeName
     * @return BusServiceType
     */
    public function setTxcServiceTypeName($txcServiceTypeName)
    {
        $this->txcServiceTypeName = $txcServiceTypeName;

        return $this;
    }

    /**
     * Get the txc service type name
     *
     * @return string
     */
    public function getTxcServiceTypeName()
    {
        return $this->txcServiceTypeName;
    }

}
