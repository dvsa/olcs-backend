<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrfoPsvAuth Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irfo_psv_auth",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_psv_auth_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_journey_frequency", columns={"journey_frequency"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_irfo_psv_auth_type_id", columns={"irfo_psv_auth_type_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_status", columns={"status"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_withdrawn_reason", columns={"withdrawn_reason"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_psv_auth_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class IrfoPsvAuth extends AbstractIrfoPsvAuth
{
    const STATUS_APPROVED = 'irfo_auth_s_approved';
    const STATUS_CNS = 'irfo_auth_s_cns';
    const STATUS_GRANTED = 'irfo_auth_s_granted';
    const STATUS_PENDING = 'irfo_auth_s_pending';
    const STATUS_REFUSED = 'irfo_auth_s_refused';
    const STATUS_RENEW = 'irfo_auth_s_renew';
    const STATUS_WITHDRAWN = 'irfo_auth_s_withdrawn';

    const JOURNEY_FREQ_DAILY = 'psv_freq_daily';
    const JOURNEY_FREQ_TWICE_WEEKLY = 'psv_freq_2_weekly';
    const JOURNEY_FREQ_WEEKLY = 'psv_freq_weekly';
    const JOURNEY_FREQ_FORTNIGHTLY = 'psv_freq_fortnight';
    const JOURNEY_FREQ_MONTHLY = 'psv_freq_monthly';
    const JOURNEY_FREQ_OTHER = 'psv_freq_other';

    public function __construct(Organisation $organisation, IrfoPsvAuthType $type, RefData $status)
    {
        $this->organisation = $organisation;
        $this->irfoPsvAuthType = $type;
        $this->status = $status;
    }
}
