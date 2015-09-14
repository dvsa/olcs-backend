<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

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

    /**
     * Update
     *
     * @param IrfoGvPermitType $irfoGvPermitType
     * @param int $yearRequired
     * @param \DateTime $inForceDate
     * @param \DateTime $expiryDate
     * @param int $noOfCopies
     * @param string $isFeeExempt
     * @param string $exemptionDetails
     * @param string $irfoFeeId
     * @return IrfoGvPermit
     */
    public function update(
        IrfoGvPermitType $irfoGvPermitType,
        $yearRequired,
        \DateTime $inForceDate,
        \DateTime $expiryDate,
        $noOfCopies,
        $isFeeExempt = null,
        $exemptionDetails = null,
        $irfoFeeId = null
    ) {
        // validate
        if ($expiryDate < $inForceDate) {
            throw new ValidationException(['Expiry date must be after or the same as in force date']);
        }

        // update
        $this->irfoGvPermitType = $irfoGvPermitType;
        $this->yearRequired = $yearRequired;
        $this->inForceDate = $inForceDate;
        $this->expiryDate = $expiryDate;
        $this->noOfCopies = $noOfCopies;

        if ($isFeeExempt !== null) {
            $this->isFeeExempt = $isFeeExempt;
        }

        if ($exemptionDetails !== null) {
            $this->exemptionDetails = $exemptionDetails;
        }

        if ($irfoFeeId !== null) {
            $this->irfoFeeId = $irfoFeeId;
        }

        return $this;
    }

    /**
     * Reset
     *
     * @param RefData $status
     * @return IrfoGvPermit
     */
    public function reset(RefData $status)
    {
        if ($status->getId() !== self::STATUS_PENDING) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->setIrfoPermitStatus($status);

        return $this;
    }

    /**
     * Withdraw
     *
     * @param RefData $status
     * @return IrfoGvPermit
     */
    public function withdraw(RefData $status)
    {
        if ($status->getId() !== self::STATUS_WITHDRAWN) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->setIrfoPermitStatus($status);

        return $this;
    }

    /**
     * Refuse
     *
     * @param RefData $status
     * @return IrfoGvPermit
     */
    public function refuse(RefData $status)
    {
        if ($status->getId() !== self::STATUS_REFUSED) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->setIrfoPermitStatus($status);

        return $this;
    }

    /**
     * Approve
     *
     * @param RefData $status
     * @param array $fees
     * @return IrfoGvPermit
     */
    public function approve(RefData $status, array $fees)
    {
        if ($status->getId() !== self::STATUS_APPROVED) {
            throw new BadRequestException('Please provide a valid status');
        }

        if ($this->isApprovable($fees) !== true) {
            throw new BadRequestException('The record is not approvable');
        }

        $this->irfoPermitStatus = $status;

        return $this;
    }

    /**
     * Returns whether a record is approvable
     *
     * @param array $fees
     * @return bool
     */
    public function isApprovable($fees)
    {
        if ($this->irfoPermitStatus->getId() !== self::STATUS_PENDING) {
            // only record in pending state can be approved
            return false;
        }

        if ((false === $this->isApprovableBasedOnFees($fees))) {
            // record with a fee which makes it non-approvable
            return false;
        }

        return true;
    }

    /**
     * Returns whether a record has a fees which makes it approvable
     *
     * @param array $fees
     * @return bool
     */
    private function isApprovableBasedOnFees(array $fees)
    {
        if (empty($fees)) {
            // no fee makes it non-approvable
            return false;
        }

        foreach ($fees as $fee) {
            if ($fee->getFeeStatus()->getId() !== FeeEntity::STATUS_PAID) {
                // all fees must be paid
                return false;
            }
        }

        return true;
    }
}
