<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceVehicle Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence_vehicle",
 *    indexes={
 *        @ORM\Index(name="ix_licence_vehicle_vehicle_id", columns={"vehicle_id"}),
 *        @ORM\Index(name="ix_licence_vehicle_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_vehicle_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_licence_vehicle_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_licence_vehicle_interim_application_id", columns={"interim_application_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_licence_id_licence_id", columns={"licence_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_vehicle_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class LicenceVehicle extends AbstractLicenceVehicle
{

}
