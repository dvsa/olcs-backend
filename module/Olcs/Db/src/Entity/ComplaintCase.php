<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ComplaintCase Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="complaint_case",
 *    indexes={
 *        @ORM\Index(name="fk_complaint_case_complaint1_idx", columns={"complaint_id"}),
 *        @ORM\Index(name="fk_complaint_case_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_complaint_case_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_A7094FAFCF10D4F5", columns={"case_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="case_id", columns={"case_id","complaint_id"})
 *    }
 * )
 */
class ComplaintCase implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ComplaintManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}
