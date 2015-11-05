<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
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
     * Is paid for? Yes if application fee exists and is paid or waived
     *
     * @param null $applicationFeeStatusId
     * @return bool
     */
    private function isPaidFor($applicationFeeStatusId = null)
    {
        if (!empty($applicationFeeStatusId) &&
            in_array($applicationFeeStatusId, [Fee::STATUS_PAID, Fee::STATUS_WAIVED])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Is in a grantable state
     *
     * @return bool
     */
    private function isGrantableState()
    {
        if (in_array($this->getStatus()->getId(), [self::STATUS_RENEW, self::STATUS_PENDING])) {
            return true;
        }

        return false;
    }

    /**
     * Is grantable? Yes if entity status is renew/pending, and application fee exists and is paid or waived
     * 
     * @param null $applicationFee
     * @return bool
     */
    public function isGrantable($applicationFee = null)
    {
        if ($applicationFee instanceof Fee) {
            $applicationFeeStatusId = $applicationFee->getFeeStatus()->getId();

            if ($this->isPaidFor($applicationFeeStatusId) && $this->isGrantableState()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grant
     *
     * @param RefData $status
     * @param array $fees
     * @return IrfoGvPermit
     */
    public function grant(RefData $status, Fee $applicationFee)
    {
        if (!$this->isGrantable($applicationFee)) {
            throw new BadRequestException(
                ['Irfo Psv Auth is not grantable']
            );
        }

        $this->setStatus($status);

        return $this;
    }
}
