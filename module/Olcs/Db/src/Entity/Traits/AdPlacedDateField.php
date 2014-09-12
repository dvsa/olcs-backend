<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ad placed date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AdPlacedDateField
{
    /**
     * Ad placed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="ad_placed_date", nullable=true)
     */
    protected $adPlacedDate;

    /**
     * Set the ad placed date
     *
     * @param \DateTime $adPlacedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlacedDate($adPlacedDate)
    {
        $this->adPlacedDate = $adPlacedDate;

        return $this;
    }

    /**
     * Get the ad placed date
     *
     * @return \DateTime
     */
    public function getAdPlacedDate()
    {
        return $this->adPlacedDate;
    }
}
