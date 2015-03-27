<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SubCategoryDescription Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="sub_category_description",
 *    indexes={
 *        @ORM\Index(name="ix_sub_category_description_sub_category_id", columns={"sub_category_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_sub_category_description", columns={"sub_category_id","description"})
 *    }
 * )
 */
class SubCategoryDescription implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Description100Field,
        Traits\IdIdentity,
        Traits\SubCategoryManyToOne;
}
