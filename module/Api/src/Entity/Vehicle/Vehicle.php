<?php

namespace Dvsa\Olcs\Api\Entity\Vehicle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicle Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="vehicle",
 *    indexes={
 *        @ORM\Index(name="ix_vehicle_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_vehicle_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_vehicle_psv_type", columns={"psv_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_vehicle_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Vehicle extends AbstractVehicle
{

}
