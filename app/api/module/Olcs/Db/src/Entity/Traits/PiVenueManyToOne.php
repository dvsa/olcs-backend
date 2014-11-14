<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pi venue many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PiVenueManyToOne
{
    /**
     * Pi venue
     *
     * @var \Olcs\Db\Entity\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiVenue", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_venue_id", referencedColumnName="id", nullable=true)
     */
    protected $piVenue;

    /**
     * Set the pi venue
     *
     * @param \Olcs\Db\Entity\PiVenue $piVenue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiVenue($piVenue)
    {
        $this->piVenue = $piVenue;

        return $this;
    }

    /**
     * Get the pi venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getPiVenue()
    {
        return $this->piVenue;
    }
}
