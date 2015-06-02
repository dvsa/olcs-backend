<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationOperatingCentre Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_operating_centre",
 *    indexes={
 *        @ORM\Index(name="ix_application_operating_centre_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_s4_id", columns={"s4_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_application_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class ApplicationOperatingCentre extends AbstractApplicationOperatingCentre
{
    const ACTION_ADD    = 'A';
    const ACTION_UPDATE = 'U';
    const ACTION_DELETE = 'D';
}
