<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Safety status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait SafetyStatusField
{
    /**
     * Safety status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_status", nullable=true)
     */
    protected $safetyStatus;

    /**
     * Set the safety status
     *
     * @param int $safetyStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSafetyStatus($safetyStatus)
    {
        $this->safetyStatus = $safetyStatus;

        return $this;
    }

    /**
     * Get the safety status
     *
     * @return int
     */
    public function getSafetyStatus()
    {
        return $this->safetyStatus;
    }
}
