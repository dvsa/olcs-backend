<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoGvPermit Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irfo_gv_permit",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_gv_permit_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_irfo_gv_permit_type_id", columns={"irfo_gv_permit_type_id"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_irfo_permit_status", columns={"irfo_permit_status"}),
 *        @ORM\Index(name="ix_irfo_gv_permit_withdrawn_reason", columns={"withdrawn_reason"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_gv_permit_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class IrfoGvPermit extends AbstractIrfoGvPermit
{

}
