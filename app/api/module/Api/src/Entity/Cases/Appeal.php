<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Appeal Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="appeal",
 *    indexes={
 *        @ORM\Index(name="ix_appeal_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_appeal_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_appeal_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_appeal_reason", columns={"reason"}),
 *        @ORM\Index(name="ix_appeal_outcome", columns={"outcome"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_appeal_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Appeal extends AbstractAppeal
{

}
