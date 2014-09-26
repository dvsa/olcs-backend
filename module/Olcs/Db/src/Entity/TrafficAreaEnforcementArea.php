<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TrafficAreaEnforcementArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="traffic_area_enforcement_area",
 *    indexes={
 *        @ORM\Index(name="IDX_85681EEFDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_85681EEF65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_85681EEF6522907", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="IDX_85681EEF18E0B1DB", columns={"traffic_area_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="traffic_area_enforcement_area_unique", columns={"traffic_area_id","enforcement_area_id"})
 *    }
 * )
 */
class TrafficAreaEnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\EnforcementAreaManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
