<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cancelled date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CancelledDateField
{
    /**
     * Cancelled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancelled_date", nullable=true)
     */
    protected $cancelledDate;

    /**
     * Set the cancelled date
     *
     * @param \DateTime $cancelledDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get the cancelled date
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }
}
