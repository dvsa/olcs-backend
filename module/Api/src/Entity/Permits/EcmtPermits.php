<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermits Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permits",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permits_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permits_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permits_ecmt_permits_application_id",
     *     columns={"ecmt_permits_application_id"})
 *    }
 * )
 */
class EcmtPermits extends AbstractEcmtPermits
{

}
