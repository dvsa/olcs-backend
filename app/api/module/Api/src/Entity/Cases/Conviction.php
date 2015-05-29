<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conviction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="conviction",
 *    indexes={
 *        @ORM\Index(name="ix_conviction_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_conviction_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_conviction_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_conviction_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_conviction_defendant_type", columns={"defendant_type"}),
 *        @ORM\Index(name="ix_conviction_conviction_category", columns={"conviction_category"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_conviction_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Conviction extends AbstractConviction
{

}
