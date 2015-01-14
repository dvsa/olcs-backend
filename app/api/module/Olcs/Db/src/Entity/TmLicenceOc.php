<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmLicenceOc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_licence_oc",
 *    indexes={
 *        @ORM\Index(name="fk_tm_licence_oc_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_tm_licence_oc_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_tm_licence_oc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_licence_oc_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmLicenceOc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OperatingCentreManyToOne,
        Traits\CustomVersionField;

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
