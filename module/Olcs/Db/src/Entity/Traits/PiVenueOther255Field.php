<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pi venue other255 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PiVenueOther255Field
{
    /**
     * Pi venue other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_venue_other", length=255, nullable=true)
     */
    protected $piVenueOther;

    /**
     * Set the pi venue other
     *
     * @param string $piVenueOther
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiVenueOther($piVenueOther)
    {
        $this->piVenueOther = $piVenueOther;

        return $this;
    }

    /**
     * Get the pi venue other
     *
     * @return string
     */
    public function getPiVenueOther()
    {
        return $this->piVenueOther;
    }
}
