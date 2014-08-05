<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SiCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="si_category",
 *    indexes={
 *        @ORM\Index(name="fk_si_category_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_category_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SiCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\DeletedDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
