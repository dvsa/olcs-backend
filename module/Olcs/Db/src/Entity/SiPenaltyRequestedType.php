<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenaltyRequestedType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty_requested_type",
 *    indexes={
 *        @ORM\Index(name="IDX_6E1F580CDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_6E1F580C65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenaltyRequestedType implements Interfaces\EntityInterface
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
