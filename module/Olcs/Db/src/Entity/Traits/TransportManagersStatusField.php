<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transport managers status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TransportManagersStatusField
{
    /**
     * Transport managers status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="transport_managers_status", nullable=true)
     */
    protected $transportManagersStatus;

    /**
     * Set the transport managers status
     *
     * @param int $transportManagersStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTransportManagersStatus($transportManagersStatus)
    {
        $this->transportManagersStatus = $transportManagersStatus;

        return $this;
    }

    /**
     * Get the transport managers status
     *
     * @return int
     */
    public function getTransportManagersStatus()
    {
        return $this->transportManagersStatus;
    }
}
