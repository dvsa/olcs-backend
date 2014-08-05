<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Removal reason many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait RemovalReasonManyToOne
{
    /**
     * Removal reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="removal_reason", referencedColumnName="id")
     */
    protected $removalReason;

    /**
     * Set the removal reason
     *
     * @param \Olcs\Db\Entity\RefData $removalReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovalReason($removalReason)
    {
        $this->removalReason = $removalReason;

        return $this;
    }

    /**
     * Get the removal reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRemovalReason()
    {
        return $this->removalReason;
    }
}
