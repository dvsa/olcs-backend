<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceVehicleFee Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence_vehicle_fee",
 *    indexes={
 *        @ORM\Index(name="ix_licence_vehicle_fee_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_licence_vehicle_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_vehicle_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_fee_licence_vehicle_id_licence_vehicle_id", columns={"licence_vehicle_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_vehicle_fee_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class LicenceVehicleFee extends AbstractLicenceVehicleFee
{

}
