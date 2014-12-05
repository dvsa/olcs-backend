<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Closed date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait ClosedDateField
{
    /**
     * Closed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="closed_date", nullable=true)
     */
    protected $closedDate;

    /**
     * Set the closed date
     *
     * @param \DateTime $closedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setClosedDate($closedDate)
    {
        $this->closedDate = $closedDate;

        return $this;
    }

    /**
     * Get the closed date
     *
     * @return \DateTime
     */
    public function getClosedDate()
    {
        return $this->closedDate;
    }
}
