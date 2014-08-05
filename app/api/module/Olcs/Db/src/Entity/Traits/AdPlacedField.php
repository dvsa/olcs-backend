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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

    /**
     * Set the ad placed
     *
     * @param boolean $adPlaced
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
     * @return boolean
     */
    public function getAdPlaced()
    {
        return $this->adPlaced;
    }
}
