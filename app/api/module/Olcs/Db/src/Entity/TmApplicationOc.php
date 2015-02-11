<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TmApplicationOc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tm_application_oc",
 *    indexes={
 *        @ORM\Index(name="fk_tm_application_oc_transport_manager_application1_idx", columns={"transport_manager_application_id"}),
 *        @ORM\Index(name="fk_tm_application_oc_operating_centre1_idx", columns={"operating_centre_id"})
 *    }
 * )
 */
class TmApplicationOc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OperatingCentreManyToOne;

    /**
     * Transport manager application
     *
     * @var \Olcs\Db\Entity\TransportManagerApplication
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerApplication", inversedBy="tmApplicationOcs")
     * @ORM\JoinColumn(name="transport_manager_application_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManagerApplication;

    /**
     * Set the transport manager application
     *
     * @param \Olcs\Db\Entity\TransportManagerApplication $transportManagerApplication
     * @return TmApplicationOc
     */
    public function setTransportManagerApplication($transportManagerApplication)
    {
        $this->transportManagerApplication = $transportManagerApplication;

        return $this;
    }

    /**
     * Get the transport manager application
     *
     * @return \Olcs\Db\Entity\TransportManagerApplication
     */
    public function getTransportManagerApplication()
    {
        return $this->transportManagerApplication;
    }
}
