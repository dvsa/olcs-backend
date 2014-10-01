<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * WaiveReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="waive_reason",
 *    indexes={
 *        @ORM\Index(name="IDX_D3FA6C2765CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_D3FA6C27DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class WaiveReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IsIrfoField,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
