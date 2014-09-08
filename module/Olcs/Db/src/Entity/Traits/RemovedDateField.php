<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Removed date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait RemovedDateField
{
    /**
     * Removed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removed_date", nullable=true)
     */
    protected $removedDate;

    /**
     * Set the removed date
     *
     * @param \DateTime $removedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovedDate($removedDate)
    {
        $this->removedDate = $removedDate;

        return $this;
    }

    /**
     * Get the removed date
     *
     * @return \DateTime
     */
    public function getRemovedDate()
    {
        return $this->removedDate;
    }

}
