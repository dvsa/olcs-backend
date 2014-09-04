<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Effective date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait EffectiveDateField
{
    /**
     * Effective date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_date", nullable=true)
     */
    protected $effectiveDate;

    /**
     * Set the effective date
     *
     * @param \DateTime $effectiveDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get the effective date
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

}
