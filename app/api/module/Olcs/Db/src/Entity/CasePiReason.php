<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CasePiReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="case_pi_reason",
 *    indexes={
 *        @ORM\Index(name="fk_case_rec_pi_reason_pi_reason1_idx", columns={"pi_reason_id"}),
 *        @ORM\Index(name="fk_case_pi_reason_pi_detail1_idx", columns={"pi_detail_id"}),
 *        @ORM\Index(name="fk_case_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_case_pi_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CasePiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PiDetailManyToOne,
        Traits\PiReasonManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
