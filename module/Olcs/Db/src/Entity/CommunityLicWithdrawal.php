<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CommunityLicWithdrawal Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CommunityLicManyToOne,
        Traits\StartDateFieldAlt1,
        Traits\EndDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
