<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Start date field alt1 trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait StartDateFieldAlt1
{
    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date", nullable=true)
     */
    protected $startDate;

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
