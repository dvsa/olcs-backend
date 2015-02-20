<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Business type status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait BusinessTypeStatusField
{
    /**
     * Business type status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="business_type_status", nullable=true)
     */
    protected $businessTypeStatus;

    /**
     * Set the business type status
     *
     * @param int $businessTypeStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusinessTypeStatus($businessTypeStatus)
    {
        $this->businessTypeStatus = $businessTypeStatus;

        return $this;
    }

    /**
     * Get the business type status
     *
     * @return int
     */
    public function getBusinessTypeStatus()
    {
        return $this->businessTypeStatus;
    }
}
