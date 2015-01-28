<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Purchase date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PurchaseDateField
{
    /**
     * Purchase date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="purchase_date", nullable=true)
     */
    protected $purchaseDate;

    /**
     * Set the purchase date
     *
     * @param \DateTime $purchaseDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get the purchase date
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }
}
