<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * WaiveReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="waive_reason",
 *    indexes={
 *        @ORM\Index(name="fk_waive_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_waive_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class WaiveReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IsIrfoField,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
