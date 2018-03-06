<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermits Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permits",
 *    indexes={
 *        @ORM\Index(name="ecmt_ecmt_permits_application_id", columns={"ecmt_permits_application_id"}),
 *        @ORM\Index(name="ecmt_sector_id", columns={"sector_id"}),
 *        @ORM\Index(name="ecmt_payment_status_id", columns={"payment_status_id"}),
 *        @ORM\Index(name="ecmt_application_status_id", columns={"application_status_id"}),
 *        @ORM\Index(name="ecmt_ecmt_permits_created_by", columns={"created_by"})
 *    }
 * )
 */
class EcmtPermits extends AbstractEcmtPermits
{

}
