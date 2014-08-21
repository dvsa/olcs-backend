<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * End date field alt1 trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait EndDateFieldAlt1
{
    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date", nullable=true)
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
