<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reason many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait ReasonManyToOne
{
    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\Reason
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Reason", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Set the reason
     *
     * @param \Olcs\Db\Entity\Reason $reason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\Reason
     */
    public function getReason()
    {
        return $this->reason;
    }

}
