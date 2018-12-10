<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemovalsEcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="removals_ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_removals_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_removals_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_removals_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_removals_ecmt_permit_application_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class RemovalsEcmtPermitApplication extends AbstractRemovalsEcmtPermitApplication
{

}
