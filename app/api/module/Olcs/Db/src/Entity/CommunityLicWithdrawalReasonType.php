<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicWithdrawalReasonType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_withdrawal_reason_type",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_withdrawal_reason_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_withdrawal_reason_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicWithdrawalReasonType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
