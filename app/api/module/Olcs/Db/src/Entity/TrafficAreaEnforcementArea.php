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
 *        @ORM\Index(name="fk_TrafficAreaVehicleInspectorate_TrafficArea1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_TrafficAreaVehicleInspectorate_VehicleInspectorate1_idx", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="fk_traffic_area_enforcement_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_traffic_area_enforcement_area_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="traffic_area_id", columns={"traffic_area_id","enforcement_area_id"})
 *    }
 * )
 */
class TrafficAreaEnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\EnforcementAreaManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }
}
