<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ceased date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CeasedDateField
{
    /**
     * Ceased date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="ceased_date", nullable=true)
     */
    protected $ceasedDate;

    /**
     * Set the ceased date
     *
     * @param \DateTime $ceasedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCeasedDate($ceasedDate)
    {
        $this->ceasedDate = $ceasedDate;

        return $this;
    }

    /**
     * Get the ceased date
     *
     * @return \DateTime
     */
    public function getCeasedDate()
    {
        return $this->ceasedDate;
    }
}
