<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ComplaintOcLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="complaint_oc_licence",
 *    indexes={
 *        @ORM\Index(name="fk_complaint_oc_licence_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_complaint_oc_licence_complaint1_idx", columns={"complaint_id"}),
 *        @ORM\Index(name="fk_complaint_oc_licence_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_complaint_oc_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_complaint_oc_licence_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ComplaintOcLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\ComplaintManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }
}
