<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * In force date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait InForceDateField
{
    /**
     * In force date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="in_force_date", nullable=true)
     */
    protected $inForceDate;

    /**
     * Set the in force date
     *
     * @param \DateTime $inForceDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setInForceDate($inForceDate)
    {
        $this->inForceDate = $inForceDate;

        return $this;
    }

    /**
     * Get the in force date
     *
     * @return \DateTime
     */
    public function getInForceDate()
    {
        return $this->inForceDate;
    }
}
