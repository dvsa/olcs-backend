<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fee many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait FeeManyToOne
{
    /**
     * Fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Fee")
     * @ORM\JoinColumn(name="fee_id", referencedColumnName="id")
     */
    protected $fee;

    /**
     * Set the fee
     *
     * @param \Olcs\Db\Entity\Fee $fee
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get the fee
     *
     * @return \Olcs\Db\Entity\Fee
     */
    public function getFee()
    {
        return $this->fee;
    }

}
