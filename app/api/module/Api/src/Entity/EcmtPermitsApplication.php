<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermitsApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permits_application",
 *    indexes={
 *        @ORM\Index(name="ecmt_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ecmt_payment_status_id", columns={"payment_status_id"}),
 *        @ORM\Index(name="ecmt_application_status_id", columns={"application_status_id"}),
 *        @ORM\Index(name="ecmt_ecmt_permits_application_created_by", columns={"created_by"})
 *    }
 * )
 */
class EcmtPermitsApplication extends AbstractEcmtPermitsApplication
{

}
