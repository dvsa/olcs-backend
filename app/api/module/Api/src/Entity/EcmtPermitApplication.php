<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ecmt_permit_application_payment_status_id", columns={"payment_status_id"}),
 *        @ORM\Index(name="ecmt_permit_application_application_status_id",
     *     columns={"application_status_id"})
 *    }
 * )
 */
class EcmtPermitApplication extends AbstractEcmtPermitApplication
{

}
