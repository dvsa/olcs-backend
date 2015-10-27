<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
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

    /**
     * Update
     * @return IrfoPsvAuth
     */
    public function update(
        IrfoPsvAuthType $type,
        RefData $status,
        $validityPeriod,
        \DateTime $inForceDate,
        $serviceRouteFrom,
        $serviceRouteTo,
        RefData $journeyFrequency,
        $copiesRequired,
        $copiesRequiredTotal
    ) {
        $this->irfoPsvAuthType = $type;
        $this->validityPeriod = $validityPeriod;
        $this->inForceDate = $inForceDate;
        $this->serviceRouteFrom = $serviceRouteFrom;
        $this->serviceRouteTo = $serviceRouteTo;
        $this->journeyFrequency = $journeyFrequency;
        $this->copiesRequired = $copiesRequired;
        $this->copiesRequiredTotal = $copiesRequiredTotal;

        // deal with IrfoFileNo
        $this->populateFileNo();

        $this->updateStatus($status);

        return $this;
    }

    /**
     * Populate File Number
     * @return IrfoPsvAuth
     */
    public function populateFileNo()
    {
        $irfoFileNo = sprintf(
            '%s/%d',
            $this->getIrfoPsvAuthType()->getSectionCode(),
            $this->getId()
        );
        $this->setIrfoFileNo($irfoFileNo);

        return $this;
    }

    /**
     * Populate Irfo Fee Id
     * IR& {0,to pad to 7 chars} &[operator ID] eg "IR0001867"
     *
     * @return IrfoPsvAuth
     */
    public function populateIrfoFeeId()
    {
        $irfoFeeId = 'IR' . str_pad($this->getOrganisation()->getId(), 7, '0', STR_PAD_LEFT);

        $this->setIrfoFeeId($irfoFeeId);

        return $this;
    }

    /**
     * Can this entity change status to granted.
     *
     * @return bool
     */
    public function isGrantable()
    {
        if (in_array($this->getStatus()->getId(), [self::STATUS_RENEW, self::STATUS_PENDING])) {
            return true;
        }
        return false;
    }

    /**
     * Updates the status of the entity. Only 'granted' logic implemented.
     *
     * @param RefData $status
     * @throws BadRequestException
     */
    private function updateStatus(RefData $status)
    {
        switch($status->getId())
        {
            case self::STATUS_GRANTED:
                if ($this->isGrantable()) {
                    $this->status = $status;
                } else {
                    throw new BadRequestException('Status ' . $status->getId() . ' not permitted');
                }
                break;
            default:
                $this->status = $status;
        }
    }
}
