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
 *        @ORM\Index(name="ix_community_lic_withdrawal_reason_type_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_withdrawal_reason_type_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicWithdrawalReasonType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
