<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pi many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PiManyToOne
{
    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=true)
     */
    protected $pi;

    /**
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Olcs\Db\Entity\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }

}
