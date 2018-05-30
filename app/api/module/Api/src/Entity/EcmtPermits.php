<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermits Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permits",
 *    indexes={
 *        @ORM\Index(name="ecmt_permits_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_permits_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ecmt_ecmt_permits_application_id", columns={"ecmt_permits_application_id"}),
 *        @ORM\Index(name="ecmt_permits_payment_status_id", columns={"payment_status_id"}),
 *        @ORM\Index(name="ecmt_permits_application_status_id", columns={"application_status_id"})
 *    }
 * )
 */
class EcmtPermits extends AbstractEcmtPermits
{

}
