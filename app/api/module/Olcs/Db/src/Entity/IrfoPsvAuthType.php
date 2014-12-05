<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoPsvAuthType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_psv_auth_type",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_psv_auth_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoPsvAuthType implements Interfaces\EntityInterface
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
