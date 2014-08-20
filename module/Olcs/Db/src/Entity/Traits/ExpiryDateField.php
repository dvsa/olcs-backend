<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Expiry date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait ExpiryDateField
{
    /**
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="expiry_date", nullable=true)
     */
    protected $expiryDate;

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }
}
