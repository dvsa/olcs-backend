<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Specified date field trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait SpecifiedDateField
{
    /**
     * Specified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="specified_date", nullable=true)
     */
    protected $specifiedDate;

    /**
     * Set the specified date
     *
     * @param \DateTime $specifiedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSpecifiedDate($specifiedDate)
    {
        $this->specifiedDate = $specifiedDate;

        return $this;
    }

    /**
     * Get the specified date
     *
     * @return \DateTime
     */
    public function getSpecifiedDate()
    {
        return $this->specifiedDate;
    }

}
