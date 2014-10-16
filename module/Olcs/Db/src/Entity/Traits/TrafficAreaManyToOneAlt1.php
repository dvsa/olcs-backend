<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Traffic area many to one alt1 trait
 *
 * Auto-Generated (Shared between 5 entities)
 */
trait TrafficAreaManyToOneAlt1
{
    /**
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

    /**
     * Set the traffic area
     *
     * @param \Olcs\Db\Entity\TrafficArea $trafficArea
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Olcs\Db\Entity\TrafficArea
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }
}
