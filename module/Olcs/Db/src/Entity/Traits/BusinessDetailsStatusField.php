<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Business details status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait BusinessDetailsStatusField
{
    /**
     * Business details status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="business_details_status", nullable=true)
     */
    protected $businessDetailsStatus;

    /**
     * Set the business details status
     *
     * @param int $businessDetailsStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusinessDetailsStatus($businessDetailsStatus)
    {
        $this->businessDetailsStatus = $businessDetailsStatus;

        return $this;
    }

    /**
     * Get the business details status
     *
     * @return int
     */
    public function getBusinessDetailsStatus()
    {
        return $this->businessDetailsStatus;
    }
}
