<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Route seq field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait RouteSeqField
{
    /**
     * Route seq
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="route_seq", nullable=false)
     */
    protected $routeSeq;

    /**
     * Set the route seq
     *
     * @param int $routeSeq
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRouteSeq($routeSeq)
    {
        $this->routeSeq = $routeSeq;

        return $this;
    }

    /**
     * Get the route seq
     *
     * @return int
     */
    public function getRouteSeq()
    {
        return $this->routeSeq;
    }
}
