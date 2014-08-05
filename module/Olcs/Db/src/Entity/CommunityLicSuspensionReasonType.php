<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CommunityLicSuspensionReasonType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="community_lic_suspension_reason_type",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_suspension_reason_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspensionReasonType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
