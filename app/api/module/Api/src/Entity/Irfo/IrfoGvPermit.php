<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

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
    const STATUS_APPROVED = 'irfo_perm_s_appreoved';
    const STATUS_PENDING = 'irfo_perm_s_pending';
    const STATUS_REFUSED = 'irfo_perm_s_refused';
    const STATUS_WITHDRAWN = 'irfo_perm_s_withdrawn';

    public function __construct(Organisation $organisation, IrfoGvPermitType $type, RefData $status)
    {
        $this->setOrganisation($organisation);
        $this->setIrfoGvPermitType($type);
        $this->setIrfoPermitStatus($status);
    }
}
