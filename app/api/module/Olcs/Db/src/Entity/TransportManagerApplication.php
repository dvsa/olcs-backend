<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerApplication Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_application",
 *    indexes={
 *        @ORM\Index(name="IDX_F531CC7865CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_F531CC78DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_F531CC783E030ACD", columns={"application_id"}),
 *        @ORM\Index(name="IDX_F531CC781F75BD29", columns={"transport_manager_id"})
 *    }
 * )
 */
class TransportManagerApplication implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
