<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoPsvAuthNumber Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irfo_psv_auth_number",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_psv_auth_number_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_number_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_number_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoPsvAuthNumber extends AbstractIrfoPsvAuthNumber
{

}
