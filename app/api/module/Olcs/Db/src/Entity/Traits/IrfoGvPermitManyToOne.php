<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Irfo gv permit many to one trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait IrfoGvPermitManyToOne
{
    /**
     * Irfo gv permit
     *
     * @var \Olcs\Db\Entity\IrfoGvPermit
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoGvPermit")
     * @ORM\JoinColumn(name="irfo_gv_permit_id", referencedColumnName="id")
     */
    protected $irfoGvPermit;

    /**
     * Set the irfo gv permit
     *
     * @param \Olcs\Db\Entity\IrfoGvPermit $irfoGvPermit
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoGvPermit($irfoGvPermit)
    {
        $this->irfoGvPermit = $irfoGvPermit;

        return $this;
    }

    /**
     * Get the irfo gv permit
     *
     * @return \Olcs\Db\Entity\IrfoGvPermit
     */
    public function getIrfoGvPermit()
    {
        return $this->irfoGvPermit;
    }

}
