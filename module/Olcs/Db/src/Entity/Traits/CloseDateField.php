<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Close date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CloseDateField
{
    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="close_date", nullable=true)
     */
    protected $closeDate;

    /**
     * Set the close date
     *
     * @param \DateTime $closeDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get the close date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }
}
