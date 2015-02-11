<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TmLicenceOc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tm_licence_oc",
 *    indexes={
 *        @ORM\Index(name="fk_tm_licence_oc_transport_manager_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_tm_licence_oc_operating_centre1_idx", columns={"operating_centre_id"})
 *    }
 * )
 */
class TmLicenceOc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OperatingCentreManyToOne;

    /**
     * Transport manager licence
     *
     * @var \Olcs\Db\Entity\TransportManagerLicence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerLicence", inversedBy="tmLicenceOcs")
     * @ORM\JoinColumn(name="transport_manager_licence_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManagerLicence;

    /**
     * Set the transport manager licence
     *
     * @param \Olcs\Db\Entity\TransportManagerLicence $transportManagerLicence
     * @return TmLicenceOc
     */
    public function setTransportManagerLicence($transportManagerLicence)
    {
        $this->transportManagerLicence = $transportManagerLicence;

        return $this;
    }

    /**
     * Get the transport manager licence
     *
     * @return \Olcs\Db\Entity\TransportManagerLicence
     */
    public function getTransportManagerLicence()
    {
        return $this->transportManagerLicence;
    }
}
