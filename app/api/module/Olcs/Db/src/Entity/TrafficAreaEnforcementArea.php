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
 *        @ORM\Index(name="ix_traffic_area_enforcement_area_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_traffic_area_enforcement_area_enforcement_area_id", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="ix_traffic_area_enforcement_area_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_traffic_area_enforcement_area_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_ta_enforcement_area_traffic_area_id_enforcement_area_id", columns={"traffic_area_id","enforcement_area_id"})
 *    }
 * )
 */
class TrafficAreaEnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\EnforcementAreaManyToOne,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TrafficAreaManyToOne,
        Traits\CustomVersionField;
}
