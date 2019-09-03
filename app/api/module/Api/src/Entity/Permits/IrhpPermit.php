<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermit Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_range_idx", columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_application1_idx",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_candidate_permit1_idx",
     *     columns={"irhp_candidate_permit_id"}),
 *        @ORM\Index(name="fk_irhp_permit_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_last_modified_by_user_id", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irhp_permit_status_ref_data_id", columns={"status"})
 *    }
 * )
 */
class IrhpPermit extends AbstractIrhpPermit
{
    const STATUS_PENDING            = 'irhp_permit_pending';
    const STATUS_AWAITING_PRINTING  = 'irhp_permit_awaiting_printing';
    const STATUS_PRINTING           = 'irhp_permit_printing';
    const STATUS_PRINTED            = 'irhp_permit_printed';
    const STATUS_ERROR              = 'irhp_permit_error';
    const STATUS_CEASED             = 'irhp_permit_ceased';
    const STATUS_TERMINATED         = 'irhp_permit_terminated';
    const STATUS_EXPIRED            = 'irhp_permit_expired';

    public static $readyToPrintStatuses = [
        self::STATUS_PENDING,
        self::STATUS_AWAITING_PRINTING,
        self::STATUS_PRINTING,
        self::STATUS_ERROR,
    ];

    public static $validStatuses = [
        self::STATUS_PENDING,
        self::STATUS_AWAITING_PRINTING,
        self::STATUS_ERROR,
        self::STATUS_PRINTING,
        self::STATUS_PRINTED,
    ];

    /**
     * Create new IrhpPermit
     *
     * @param IrhpCandidatePermit   $irhpCandidatePermit
     * @param DateTime              $issueDate
     * @param RefData               $status
     * @param int                   $permitNumber
     *
     * @return IrhpPermit
     */
    public static function createNew(
        IrhpCandidatePermit $irhpCandidatePermit,
        DateTime $issueDate,
        RefData $status,
        $permitNumber
    ) {
        $irhpPermit = new self();
        $irhpPermit->irhpCandidatePermit = $irhpCandidatePermit;
        $irhpPermit->irhpPermitApplication = $irhpCandidatePermit->getIrhpPermitApplication();
        $irhpPermit->irhpPermitRange = $irhpCandidatePermit->getIrhpPermitRange();
        $irhpPermit->issueDate = $issueDate;
        $irhpPermit->status = $status;
        $irhpPermit->permitNumber = $permitNumber;

        return $irhpPermit;
    }

    /**
     * Create new IrhpPermit
     *
     * @param IrhpPermit $oldPermit
     * @param IrhpPermitRange $irhpPermitRange
     * @param RefData $status
     * @param int $permitNumber
     *
     * @return IrhpPermit
     */
    public static function createReplacement(
        IrhpPermit $oldPermit,
        IrhpPermitRange $irhpPermitRange,
        RefData $status,
        $permitNumber
    ) {
        $irhpPermit = new self();
        $irhpPermit->replaces = $oldPermit;
        $irhpPermit->irhpCandidatePermit = $oldPermit->getIrhpCandidatePermit();
        $irhpPermit->irhpPermitApplication = $oldPermit->getIrhpPermitApplication();
        $irhpPermit->irhpPermitRange = $irhpPermitRange;
        $irhpPermit->issueDate = new DateTime();
        $irhpPermit->expiryDate = $oldPermit->getIrhpPermitRange()->getIrhpPermitStock()->getValidTo();
        $irhpPermit->status = $status;
        $irhpPermit->permitNumber = $permitNumber;

        return $irhpPermit;
    }

    /**
     * Create new IrhpPermit during irhp permit application allocation
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param IrhpPermitRange $irhpPermitRange
     * @param DateTime $issueDate
     * @param RefData $status
     * @param int $permitNumber
     * @param DateTime|null $expiryDate
     *
     * @return IrhpPermit
     */
    public static function createForIrhpApplication(
        IrhpPermitApplication $irhpPermitApplication,
        IrhpPermitRange $irhpPermitRange,
        DateTime $issueDate,
        RefData $status,
        $permitNumber,
        ?DateTime $expiryDate
    ) {
        $irhpPermit = new self();
        $irhpPermit->irhpPermitApplication = $irhpPermitApplication;
        $irhpPermit->irhpPermitRange = $irhpPermitRange;
        $irhpPermit->issueDate = $issueDate;
        $irhpPermit->status = $status;
        $irhpPermit->permitNumber = $permitNumber;
        $irhpPermit->expiryDate = $expiryDate;

        return $irhpPermit;
    }

    /**
     * @param RefData $status
     * @return $this
     */
    public function cease(RefData $status)
    {
        // Check status provided is correct for Ceased and set expiry date to now.
        if ($status->getId() !== self::STATUS_CEASED) {
            throw new ForbiddenException('This method can only be called with refdata status id: '.self::STATUS_CEASED);
        }
        $this->status = $status;
        $this->expiryDate = new DateTime();
    }

    /**
     * Get the permit number with prefix
     *
     * @return string
     */
    public function getPermitNumberWithPrefix()
    {
        return sprintf('%s%05d', $this->getIrhpPermitRange()->getPrefix(), $this->getPermitNumber());
    }

    /**
     * Calculated values to be added to a bundle
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'permitNumberWithPrefix' => $this->getPermitNumberWithPrefix(),
            'startDate' => $this->getStartDate(),
        ];
    }

    /**
     * Get the permit start date
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        // set to stock's valid from date by default
        $startDate = $this->getIrhpPermitRange()->getIrhpPermitStock()->getValidFrom(true);

        $issueDate = $this->getIssueDate(true);

        if (isset($startDate) && isset($issueDate) && ($issueDate > $startDate)) {
            // overwrite with the issue date
            $startDate = $issueDate;
        }

        return $startDate;
    }

    /**
     * Proceed to status
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    public function proceedToStatus(RefData $status)
    {
        switch ($status->getId()) {
            case self::STATUS_AWAITING_PRINTING:
                $this->proceedToAwaitingPrinting($status);
                break;
            case self::STATUS_PRINTING:
                $this->proceedToPrinting($status);
                break;
            case self::STATUS_PRINTED:
                $this->proceedToPrinted($status);
                break;
            case self::STATUS_ERROR:
                $this->proceedToError($status);
                break;
            case self::STATUS_TERMINATED:
                $this->proceedToTerminated($status);
                break;
            default:
                throw new ForbiddenException(sprintf('Action for status %s not defined.', $status->getId()));
        }
    }

    /**
     * Proceed to awaiting printing
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    private function proceedToAwaitingPrinting(RefData $status)
    {
        if (!$this->isPending() && !$this->hasError()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to proceed to awaiting printing (%s)',
                    $this->status->getId()
                )
            );
        }

        $this->status = $status;
    }

    /**
     * Proceed to printing
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    private function proceedToPrinting(RefData $status)
    {
        if (!$this->isAwaitingPrinting()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to proceed to printing (%s)',
                    $this->status->getId()
                )
            );
        }

        $this->status = $status;
    }

    /**
     * Proceed to printed
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    private function proceedToPrinted(RefData $status)
    {
        if (!$this->isPrinting()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to proceed to printed (%s)',
                    $this->status->getId()
                )
            );
        }

        $this->status = $status;
    }

    /**
     * Proceed to error
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    private function proceedToError(RefData $status)
    {
        if ($this->hasError()) {
            // do nothing if the status is STATUS_ERROR already
            return;
        }

        if (!$this->isPending() && !$this->isAwaitingPrinting() && !$this->isPrinting()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to proceed to error (%s)',
                    $this->status->getId()
                )
            );
        }

        $this->status = $status;
    }

    /**
     * Proceed to terminated
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    private function proceedToTerminated(RefData $status)
    {
        if ($this->isCeased() || $this->isTerminated()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to be terminated (%s)',
                    $this->status->getId()
                )
            );
        }
        $this->expiryDate = new DateTime();
        $this->status = $status;
    }

    /**
     * Is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status->getId() === self::STATUS_PENDING;
    }

    /**
     * Is awaiting printing
     *
     * @return bool
     */
    public function isAwaitingPrinting()
    {
        return $this->status->getId() === self::STATUS_AWAITING_PRINTING;
    }

    /**
     * Is printing
     *
     * @return bool
     */
    public function isPrinting()
    {
        return $this->status->getId() === self::STATUS_PRINTING;
    }

    /**
     * Is printed
     *
     * @return bool
     */
    public function isPrinted()
    {
        return $this->status->getId() === self::STATUS_PRINTED;
    }

    /**
     * Has error
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->status->getId() === self::STATUS_ERROR;
    }

    /**
     * Is not ceased
     *
     * @return bool
     */
    public function isCeased()
    {
        return $this->status->getId() === self::STATUS_CEASED;
    }

    /**
     * Is terminated
     *
     * @return bool
     */
    public function isTerminated()
    {
        return $this->status->getId() === self::STATUS_TERMINATED;
    }

    /**
     * Is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status->getId() === self::STATUS_EXPIRED;
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return in_array($this->status->getId(), self::$validStatuses);
    }
}
