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
 *        @ORM\Index(name="IDX_57F70AB565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_57F70AB5DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_57F70AB5F9FDD69C", columns={"si_category_id"})
 *    }
 * )
 */
class SiCategoryType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\SiCategoryManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
