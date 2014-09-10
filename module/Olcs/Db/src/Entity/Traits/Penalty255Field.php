<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Penalty255 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Penalty255Field
{
    /**
     * Penalty
     *
     * @var string
     *
     * @ORM\Column(type="string", name="penalty", length=255, nullable=true)
     */
    protected $penalty;

    /**
     * Set the penalty
     *
     * @param string $penalty
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;

        return $this;
    }

    /**
     * Get the penalty
     *
     * @return string
     */
    public function getPenalty()
    {
        return $this->penalty;
    }
}
