<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="IDX_7706F54665CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_7706F546DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_7706F54626EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_7706F5461F75BD29", columns={"transport_manager_id"})
 *    }
 * )
 */
class TransportManagerLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
