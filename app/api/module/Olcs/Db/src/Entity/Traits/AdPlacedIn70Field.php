<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ad placed in70 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AdPlacedIn70Field
{
    /**
     * Ad placed in
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ad_placed_in", length=70, nullable=true)
     */
    protected $adPlacedIn;

    /**
     * Set the ad placed in
     *
     * @param string $adPlacedIn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlacedIn($adPlacedIn)
    {
        $this->adPlacedIn = $adPlacedIn;

        return $this;
    }

    /**
     * Get the ad placed in
     *
     * @return string
     */
    public function getAdPlacedIn()
    {
        return $this->adPlacedIn;
    }

}
