<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pi reason many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PiReasonManyToOne
{
    /**
     * Pi reason
     *
     * @var \Olcs\Db\Entity\PiReason
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiReason")
     * @ORM\JoinColumn(name="pi_reason_id", referencedColumnName="id")
     */
    protected $piReason;

    /**
     * Set the pi reason
     *
     * @param \Olcs\Db\Entity\PiReason $piReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiReason($piReason)
    {
        $this->piReason = $piReason;

        return $this;
    }

    /**
     * Get the pi reason
     *
     * @return \Olcs\Db\Entity\PiReason
     */
    public function getPiReason()
    {
        return $this->piReason;
    }
}
