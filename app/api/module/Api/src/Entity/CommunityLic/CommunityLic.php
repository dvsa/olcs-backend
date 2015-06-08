<?php

namespace Dvsa\Olcs\Api\Entity\CommunityLic;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommunityLic Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="community_lic",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_community_lic_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_community_lic_com_lic_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLic extends AbstractCommunityLic
{
    const STATUS_PENDING = 'cl_sts_pending';
    const STATUS_ACTIVE = 'cl_sts_active';
    const STATUS_EXPIRED = 'cl_sts_expired';
    const STATUS_WITHDRAWN = 'cl_sts_withdrawn';
    const STATUS_SUSPENDED = 'cl_sts_suspended';
    const STATUS_VOID = 'cl_sts_annulled';
    const STATUS_RETURNDED = 'cl_sts_returned';
}
