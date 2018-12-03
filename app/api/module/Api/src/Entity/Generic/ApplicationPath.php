<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationPath Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_path",
 *    indexes={
 *        @ORM\Index(name="fk_application_path_irhp_permit_type_id", columns={"irhp_permit_type_id"})
 *    }
 * )
 */
class ApplicationPath extends AbstractApplicationPath
{

}
