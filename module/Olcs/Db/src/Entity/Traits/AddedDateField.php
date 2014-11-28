<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Added date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AddedDateField
{
    /**
     * Added date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_date", nullable=true)
     */
    protected $addedDate;

    /**
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }
}
