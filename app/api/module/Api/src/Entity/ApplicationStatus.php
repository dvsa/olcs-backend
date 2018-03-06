<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationStatus Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_status",
 *    indexes={
 *        @ORM\Index(name="ecmt_application_status_created_by", columns={"created_by"})
 *    }
 * )
 */
class ApplicationStatus extends AbstractApplicationStatus
{

}
