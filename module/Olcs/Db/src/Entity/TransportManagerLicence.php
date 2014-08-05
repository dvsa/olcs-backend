<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TransportManagerLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_licence_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TransportManagerLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
