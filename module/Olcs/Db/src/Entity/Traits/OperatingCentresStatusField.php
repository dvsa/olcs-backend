<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operating centres status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait OperatingCentresStatusField
{
    /**
     * Operating centres status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="operating_centres_status", nullable=true)
     */
    protected $operatingCentresStatus;

    /**
     * Set the operating centres status
     *
     * @param int $operatingCentresStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOperatingCentresStatus($operatingCentresStatus)
    {
        $this->operatingCentresStatus = $operatingCentresStatus;

        return $this;
    }

    /**
     * Get the operating centres status
     *
     * @return int
     */
    public function getOperatingCentresStatus()
    {
        return $this->operatingCentresStatus;
    }
}
