<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicWithdrawal Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_withdrawal",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_withdrawal_community_lic_id", columns={"community_lic_id"}),
 *        @ORM\Index(name="ix_community_lic_withdrawal_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_withdrawal_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_withdrawal_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicWithdrawal implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CommunityLicManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EndDateFieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\StartDateField,
        Traits\CustomVersionField;
}
