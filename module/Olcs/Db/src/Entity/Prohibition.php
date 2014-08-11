<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Prohibition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="prohibition",
 *    indexes={
 *        @ORM\Index(name="fk_prohibition_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_prohibition_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_prohibition_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Prohibition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOne,
        Traits\Notes4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}
