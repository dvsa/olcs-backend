<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ad placed field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AdPlacedField
{
    /**
     * Ad placed
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

    /**
     * Set the ad placed
     *
     * @param unknown $adPlaced
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlaced($adPlaced)
    {
        $this->adPlaced = $adPlaced;

        return $this;
    }

    /**
     * Get the ad placed
     *
     * @return unknown
     */
    public function getAdPlaced()
    {
        return $this->adPlaced;
    }

}
