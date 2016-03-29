<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoPsvAuthType Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irfo_psv_auth_type",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_psv_auth_type_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_type_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_psv_auth_type_irfo_fee_type", columns={"irfo_fee_type"})
 *    }
 * )
 */
class IrfoPsvAuthType extends AbstractIrfoPsvAuthType
{

}
