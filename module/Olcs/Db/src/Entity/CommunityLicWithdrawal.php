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
 *        @ORM\Index(name="IDX_F7BB812665CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_F7BB8126DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_F7BB81269B8FCA82", columns={"community_lic_id"})
 *    }
 * )
 */
class CommunityLicWithdrawal implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CommunityLicManyToOne,
        Traits\StartDateField,
        Traits\EndDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
