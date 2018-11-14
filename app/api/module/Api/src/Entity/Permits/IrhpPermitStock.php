<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermitStock Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_stock",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_irhp_permit_types1_idx",
 *     columns={"irhp_permit_type_id"})
 *    }
 * )
 */
class IrhpPermitStock extends AbstractIrhpPermitStock
{
    const STATUS_SCORING_NEVER_RUN = 'stock_scoring_never_run';
    const STATUS_SCORING_PENDING = 'stock_scoring_pending';
    const STATUS_SCORING_IN_PROGRESS = 'stock_scoring_in_progress';
    const STATUS_SCORING_SUCCESSFUL = 'stock_scoring_successful';
    const STATUS_SCORING_PREREQUISITE_FAIL = 'stock_scoring_prereq_fail';
    const STATUS_SCORING_UNEXPECTED_FAIL = 'stock_scoring_unexpected_fail';
    const STATUS_ACCEPT_PENDING = 'stock_accept_pending';
    const STATUS_ACCEPT_IN_PROGRESS = 'stock_accept_in_progress';
    const STATUS_ACCEPT_SUCCESSFUL = 'stock_accept_successful';
    const STATUS_ACCEPT_PREREQUISITE_FAIL = 'stock_accept_prereq_fail';
    const STATUS_ACCEPT_UNEXPECTED_FAIL = 'stock_accept_unexpected_fail';

    public static function create($type, $validFrom, $validTo, $quota, RefData $status)
    {
        $instance = new self;

        $instance->irhpPermitType = $type;
        $instance->validFrom = static::processDate($validFrom, 'Y-m-d');
        $instance->validTo = static::processDate($validTo, 'Y-m-d');
        $instance->initialStock = intval($quota) > 0 ? $quota : 0;
        $instance->status = $status;

        return $instance;
    }

    public function update($type, $validFrom, $validTo, $quota)
    {
        $this->irhpPermitType = $type;
        $this->validFrom = static::processDate($validFrom, 'Y-m-d');
        $this->validTo = static::processDate($validTo, 'Y-m-d');
        $this->initialStock = intval($quota) > 0 ? $quota : 0;

        return $this;
    }

    public function canDelete()
    {
        return
            count($this->irhpPermitRanges) === 0 &&
            count($this->irhpPermitWindows) === 0;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return ['canDelete' => $this->canDelete()];
    }

    /**
     * Returns the description of the status of this stock
     *
     * @return string
     */
    public function getStatusDescription()
    {
        return $this->status->getDescription();
    }

    /**
     * Indicates if the status of the stock allows the run scoring process to be queued for execution (subject to
     * further checks outside the boundary of this entity)
     *
     * @return bool
     */
    public function statusAllowsQueueRunScoring()
    {
        return in_array(
            $this->status->getId(),
            [
                self::STATUS_SCORING_NEVER_RUN,
                self::STATUS_SCORING_SUCCESSFUL,
                self::STATUS_SCORING_PREREQUISITE_FAIL,
                self::STATUS_SCORING_UNEXPECTED_FAIL,
                self::STATUS_ACCEPT_PREREQUISITE_FAIL
            ]
        );
    }

    /**
     * Indicates if the status of the stock allows the run scoring process to be executed (subject to further checks
     * outside the boundary of this entity)
     *
     * @return bool
     */
    public function statusAllowsRunScoring()
    {
        return $this->isScoringPending();
    }

    /**
     * Indicates if the status of the stock allows the accept scoring process to be queued for execution (subject to
     * further checks outside the boundary of this entity)
     *
     * @return bool
     */
    public function statusAllowsQueueAcceptScoring()
    {
        return in_array(
            $this->status->getId(),
            [
                self::STATUS_SCORING_SUCCESSFUL,
                self::STATUS_ACCEPT_PREREQUISITE_FAIL,
                self::STATUS_ACCEPT_UNEXPECTED_FAIL,
            ]
        );
    }

    /**
     * Indicates if the status of the stock allows the accept scoring process to be executed (subject to further checks
     * outside the boundary of this entity)
     *
     * @return bool
     */
    public function statusAllowsAcceptScoring()
    {
        return $this->isAcceptPending();
    }

    /**
     * Update the status of the stock to indicate that the scoring process has been queued
     *
     * @param RefData $scoringPendingStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToScoringPending(RefData $scoringPendingStatus)
    {
        if (!$this->statusAllowsQueueRunScoring()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to scoring pending (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $scoringPendingStatus;
    }

    /**
     * Update the status of the stock to indicate that the scoring process could not start due to one or more failed
     * failed prerequisites
     *
     * @param RefData $scoringPrerequisiteFailStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToScoringPrerequisiteFail(RefData $scoringPrerequisiteFailStatus)
    {
        if (!$this->isScoringPending()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to scoring prerequisite fail (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $scoringPrerequisiteFailStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that the scoring process is in progress
     *
     * @param RefData $scoringInProgressStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToScoringInProgress(RefData $scoringInProgressStatus)
    {
        if (!$this->isScoringPending()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to scoring in progress (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $scoringInProgressStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that the scoring process has been successfully completed
     *
     * @param RefData $scoringSuccessfulStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToScoringSuccessful(RefData $scoringSuccessfulStatus)
    {
        if (!$this->isScoringInProgress()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to scoring successful (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $scoringSuccessfulStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that an unexpected failure happened whilst stock scoring
     * was in progress
     *
     * @param RefData $scoringUnexpectedFailStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToScoringUnexpectedFail(RefData $scoringUnexpectedFailStatus)
    {
        if (!$this->isScoringInProgress()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to scoring unexpected fail (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $scoringUnexpectedFailStatus;
    }

   /**
     * Update the status of the stock to indicate that the accept process has been queued
     *
     * @param RefData $acceptPendingStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAcceptPending(RefData $acceptPendingStatus)
    {
        if (!$this->statusAllowsQueueAcceptScoring()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to accept pending (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $acceptPendingStatus;
    }

    /**
     * Update the status of the stock to indicate that the accept process could not start due to one or more failed
     * failed prerequisites
     *
     * @param RefData $acceptPrerequisiteFailStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAcceptPrerequisiteFail(RefData $acceptPrerequisiteFailStatus)
    {
        if (!$this->isAcceptPending()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to accept prerequisite fail (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $acceptPrerequisiteFailStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that the accept process is in progress
     *
     * @param RefData $acceptInProgressStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAcceptInProgress(RefData $acceptInProgressStatus)
    {
        if (!$this->isAcceptPending()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to accept in progress (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $acceptInProgressStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that the accept process has been successfully completed
     *
     * @param RefData $acceptSuccessfulStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAcceptSuccessful(RefData $acceptSuccessfulStatus)
    {
        if (!$this->isAcceptInProgress()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to accept successful (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $acceptSuccessfulStatus;
    }

    /**
     * Attempt to update the status of the stock to indicate that an unexpected failure happened whilst stock accept
     * was in progress
     *
     * @param RefData $acceptUnexpectedFailStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAcceptUnexpectedFail(RefData $acceptUnexpectedFailStatus)
    {
        if (!$this->isAcceptInProgress()) {
            throw new ForbiddenException(
                sprintf(
                    'This stock is not in the correct status to proceed to accept unexpected fail (%s)',
                    $this->getStatusDescription()
                )
            );
        }

        $this->status = $acceptUnexpectedFailStatus;
    }

    /**
     * Indicates if the running of the scoring process is currently queued on this stock
     *
     * @return bool
     */
    private function isScoringPending()
    {
        return $this->status->getId() == self::STATUS_SCORING_PENDING;
    }

    /**
     * Indicates if the running of the scoring process is currently in progress on this stock
     *
     * @return bool
     */
    private function isScoringInProgress()
    {
        return $this->status->getId() == self::STATUS_SCORING_IN_PROGRESS;
    }

    /**
     * Indicates if the running of the acceptance process is currently queued on this stock
     *
     * @return bool
     */
    private function isAcceptPending()
    {
        return $this->status->getId() == self::STATUS_ACCEPT_PENDING;
    }

    /**
     * Indicates if the running of the acceptance process is currently in progress on this stock
     *
     * @return bool
     */
    private function isAcceptInProgress()
    {
        return $this->status->getId() == self::STATUS_ACCEPT_IN_PROGRESS;
    }
}
