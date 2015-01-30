<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transport manager many to one alt1 trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait TransportManagerManyToOneAlt1
{
    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

    /**
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }
}
