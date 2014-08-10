<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiCategoryType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_category_type",
 *    indexes={
 *        @ORM\Index(name="fk_si_category_type_si_category1_idx", columns={"si_category_id"}),
 *        @ORM\Index(name="fk_si_category_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_category_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SiCategoryType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\SiCategoryManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }
}
