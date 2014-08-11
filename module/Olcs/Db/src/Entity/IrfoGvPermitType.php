<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoGvPermitType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_gv_permit_type",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_gv_permit_type_irfo_country1_idx", columns={"irfo_country_id"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_gv_permit_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoGvPermitType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IrfoCountryManyToOne,
        Traits\Description100FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}
