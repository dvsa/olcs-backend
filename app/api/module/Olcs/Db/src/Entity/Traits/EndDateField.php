<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * End date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait EndDateField
{
    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date", nullable=true)
     */
    protected $endDate;

    /**
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

}
