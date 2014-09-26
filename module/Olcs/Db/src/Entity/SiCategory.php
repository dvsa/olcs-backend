<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_category",
 *    indexes={
 *        @ORM\Index(name="IDX_EF30E2ABDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_EF30E2AB65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class SiCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
