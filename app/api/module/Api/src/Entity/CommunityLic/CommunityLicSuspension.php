<?php

namespace Dvsa\Olcs\Api\Entity\CommunityLic;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommunityLicSuspension Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="community_lic_suspension",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_suspension_community_lic_id", columns={"community_lic_id"}),
 *        @ORM\Index(name="ix_community_lic_suspension_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_suspension_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_suspension_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicSuspension extends AbstractCommunityLicSuspension
{

}
