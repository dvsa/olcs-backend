<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adjourned date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AdjournedDateField
{
    /**
     * Adjourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="adjourned_date", nullable=true)
     */
    protected $adjournedDate;

    /**
     * Set the adjourned date
     *
     * @param \DateTime $adjournedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdjournedDate($adjournedDate)
    {
        $this->adjournedDate = $adjournedDate;

        return $this;
    }

    /**
     * Get the adjourned date
     *
     * @return \DateTime
     */
    public function getAdjournedDate()
    {
        return $this->adjournedDate;
    }
}
