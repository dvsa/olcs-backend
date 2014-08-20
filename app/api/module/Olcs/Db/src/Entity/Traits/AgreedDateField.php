<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agreed date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AgreedDateField
{
    /**
     * Agreed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="agreed_date", nullable=true)
     */
    protected $agreedDate;

    /**
     * Set the agreed date
     *
     * @param \DateTime $agreedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAgreedDate($agreedDate)
    {
        $this->agreedDate = $agreedDate;

        return $this;
    }

    /**
     * Get the agreed date
     *
     * @return \DateTime
     */
    public function getAgreedDate()
    {
        return $this->agreedDate;
    }
}
