<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LicenceVehicleFee Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="licence_vehicle_fee",
 *    indexes={
 *        @ORM\Index(name="fk_licence_vehicle_fee_fee1_idx", columns={"fee_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_fee_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_fee_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_fee_licence_vehicle1", columns={"licence_vehicle_id"})
 *    }
 * )
 */
class LicenceVehicleFee implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\FeeManyToOne,
        Traits\LicenceVehicleManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}
