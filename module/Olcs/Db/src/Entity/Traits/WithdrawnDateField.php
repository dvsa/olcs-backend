<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Withdrawn date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait WithdrawnDateField
{
    /**
     * Withdrawn date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="withdrawn_date", nullable=true)
     */
    protected $withdrawnDate;

    /**
     * Set the withdrawn date
     *
     * @param \DateTime $withdrawnDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnDate($withdrawnDate)
    {
        $this->withdrawnDate = $withdrawnDate;

        return $this;
    }

    /**
     * Get the withdrawn date
     *
     * @return \DateTime
     */
    public function getWithdrawnDate()
    {
        return $this->withdrawnDate;
    }
}
