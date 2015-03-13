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
