<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tot auth trailers field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TotAuthTrailersField
{
    /**
     * Tot auth trailers
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_trailers", nullable=true)
     */
    protected $totAuthTrailers;

    /**
     * Set the tot auth trailers
     *
     * @param int $totAuthTrailers
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthTrailers($totAuthTrailers)
    {
        $this->totAuthTrailers = $totAuthTrailers;

        return $this;
    }

    /**
     * Get the tot auth trailers
     *
     * @return int
     */
    public function getTotAuthTrailers()
    {
        return $this->totAuthTrailers;
    }
}
