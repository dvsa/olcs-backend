<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Addresses status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AddressesStatusField
{
    /**
     * Addresses status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="addresses_status", nullable=true)
     */
    protected $addressesStatus;

    /**
     * Set the addresses status
     *
     * @param int $addressesStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAddressesStatus($addressesStatus)
    {
        $this->addressesStatus = $addressesStatus;

        return $this;
    }

    /**
     * Get the addresses status
     *
     * @return int
     */
    public function getAddressesStatus()
    {
        return $this->addressesStatus;
    }
}
