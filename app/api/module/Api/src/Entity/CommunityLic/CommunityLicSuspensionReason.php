<?php

namespace Dvsa\Olcs\Api\Entity\CommunityLic;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommunityLicSuspensionReason Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="community_lic_suspension_reason",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_suspension_reason_community_lic_suspension_id", columns={"community_lic_suspension_id"}),
 *        @ORM\Index(name="ix_community_lic_suspension_reason_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_suspension_reason_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_ref_data1_idx", columns={"type_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_suspension_reason_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicSuspensionReason extends AbstractCommunityLicSuspensionReason
{
    public function updateReason($withdrawal, $type, $startDate, $endDate)
    {
        $this->communityLicSuspension = $withdrawal;
        $this->type = $type;
        $this->startDate = new \DateTime($startDate);
        $this->enDate = new \DateTime($endDate);
    }
}
