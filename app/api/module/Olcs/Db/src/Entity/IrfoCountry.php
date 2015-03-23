<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoCountry Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_country",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoCountry implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description100Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
