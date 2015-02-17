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
 *        @ORM\Index(name="fk_community_lic_withdrawal_community_lic1_idx", columns={"community_lic_id"}),
 *        @ORM\Index(name="fk_community_lic_withdrawal_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_withdrawal_user2_idx", columns={"last_modified_by"})
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
        Traits\StartDateField,
        Traits\CustomVersionField;
}
