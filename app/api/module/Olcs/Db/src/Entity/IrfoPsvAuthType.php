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
 *        @ORM\Index(name="IDX_36FB95F465CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_36FB95F4DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class IrfoPsvAuthType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description100Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
