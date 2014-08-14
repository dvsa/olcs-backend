<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Venue many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait VenueManyToOne
{
    /**
     * Venue
     *
     * @var \Olcs\Db\Entity\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiVenue", fetch="LAZY")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", nullable=false)
     */
    protected $venue;

    /**
     * Set the venue
     *
     * @param \Olcs\Db\Entity\PiVenue $venue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get the venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getVenue()
    {
        return $this->venue;
    }

}
