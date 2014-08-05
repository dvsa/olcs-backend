<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deleted date field alt1 trait
 *
 * Auto-Generated (Shared between 8 entities)
 */
trait DeletedDateFieldAlt1
{
    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }
}
