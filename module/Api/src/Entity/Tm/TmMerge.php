<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TmMerge Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_merge",
 *    indexes={
 *        @ORM\Index(name="ix_tm_merge_tm_from_id", columns={"tm_from_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_to_id", columns={"tm_to_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_application_id", columns={"tm_application_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_licence_id", columns={"tm_licence_id"}),
 *        @ORM\Index(name="ix_tm_merge_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_merge_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_merge_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TmMerge extends AbstractTmMerge
{

}
