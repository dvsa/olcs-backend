<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceOperatingCentre Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence_operating_centre",
 *    indexes={
 *        @ORM\Index(name="ix_licence_operating_centre_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_licence_operating_centre_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_licence_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_operating_centre_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_licence_operating_centre_s4_id", columns={"s4_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class LicenceOperatingCentre extends AbstractLicenceOperatingCentre
{

}
