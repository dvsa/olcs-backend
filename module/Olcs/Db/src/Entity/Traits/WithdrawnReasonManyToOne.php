<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Withdrawn reason many to one trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait WithdrawnReasonManyToOne
{
    /**
     * Withdrawn reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id")
     */
    protected $withdrawnReason;

    /**
     * Set the withdrawn reason
     *
     * @param \Olcs\Db\Entity\RefData $withdrawnReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }
}
