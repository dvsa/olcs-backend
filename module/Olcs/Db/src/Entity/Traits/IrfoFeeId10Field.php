<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Irfo fee id10 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IrfoFeeId10Field
{
    /**
     * Irfo fee id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_fee_id", length=10, nullable=true)
     */
    protected $irfoFeeId;

    /**
     * Set the irfo fee id
     *
     * @param string $irfoFeeId
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoFeeId($irfoFeeId)
    {
        $this->irfoFeeId = $irfoFeeId;

        return $this;
    }

    /**
     * Get the irfo fee id
     *
     * @return string
     */
    public function getIrfoFeeId()
    {
        return $this->irfoFeeId;
    }

}
