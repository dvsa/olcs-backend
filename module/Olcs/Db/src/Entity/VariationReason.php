<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * VariationReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="variation_reason")
 */
class VariationReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Description45FieldAlt1;

    /**
     * Bus reg
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusReg", mappedBy="variationReasons")
     */
    protected $busRegs;

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

     * @return \Olcs\Db\Entity\VariationReason
     */
    public function setBusRegs($busRegs)
    {
        $this->busRegs = $busRegs;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getBusRegs()
    {
        return $this->busRegs;
    }
}
