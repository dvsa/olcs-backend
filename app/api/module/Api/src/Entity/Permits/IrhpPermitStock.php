<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\DeletableInterface;
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
class IrhpPermitStock extends AbstractIrhpPermitStock implements DeletableInterface
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

    /**
     * @param IrhpPermitType $type
     * @param Country $country
     * @param int $quota
     * @param RefData $status
     * @param mixed $validFrom
     * @param mixed $validTo
     * @return IrhpPermitStock
     * @throws ValidationException
     */
    public static function create($type, $country, $quota, RefData $status, $validFrom = null, $validTo = null)
    {
        static::validateCountry($type, $country);

        $instance = new self;

        $instance->irhpPermitType = $type;
        $instance->country = $country;
        $instance->validFrom = static::processDate($validFrom, 'Y-m-d');
        $instance->validTo = static::processDate($validTo, 'Y-m-d');
        $instance->initialStock = intval($quota) > 0 ? $quota : 0;
        $instance->status = $status;

        return $instance;
    }

    /**
     * @param IrhpPermitType $type
     * @param Country $country
     * @param int $quota
     * @param mixed $validFrom
     * @param mixed $validTo
     * @return $this
     * @throws ValidationException
     */
    public function update($type, $country, $quota, $validFrom = null, $validTo = null)
    {
        static::validateCountry($type, $country);

        $this->irhpPermitType = $type;
        $this->country = $country;
        $this->validFrom = static::processDate($validFrom, 'Y-m-d');
        $this->validTo = static::processDate($validTo, 'Y-m-d');
        $this->initialStock = intval($quota) > 0 ? $quota : 0;

        return $this;
    }

    /**
     * Enforces business logic that a Bilateral Permit MUST have a country specified
     *
     * @param $type IrhpPermitType
     * @param $country
     * @throws ValidationException
     */
    private static function validateCountry($type, $country)
    {
        if ($type->getId() === IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL && $country === null) {
            throw new ValidationException(['You must select a country for this permit type']);
        }
    }

    /**
     * Checks if there are ranges existing on the Permit Stock
     *
     * @return boolean
     */
    private function canDeletePermitRanges()
    {
        return count($this->irhpPermitRanges) === 0;
    }

    /**
     * Checks if there are windows existing on the Permit Stock.
     *
     * @return boolean
     */
    private function canDeletePermitWindows()
    {
        return count($this->irhpPermitWindows) === 0;
    }

    /**
     * Checks whether the Permit Stock can be deleted.
     *
     * @return boolean
     */
    public function canDelete()
    {
        return
            $this->canDeletePermitRanges() &&
            $this->canDeletePermitWindows();
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'canDelete' => $this->canDelete(),
            'hasEuro5Range' => $this->hasEuro5Range(),
            'hasEuro6Range' => $this->hasEuro6Range(),
            'validityYear' => $this->getValidityYear(),
        ];
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
                self::STATUS_ACCEPT_PREREQUISITE_FAIL,
                self::STATUS_ACCEPT_SUCCESSFUL,
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

    /**
     * Get non-reserved, non-replacement ranges relating to this stock ordered by from no
     *
     * @param string $emissionsCategoryId (optional)
     *
     * @return array
     */
    public function getNonReservedNonReplacementRangesOrderedByFromNo($emissionsCategoryId = null)
    {
        $criteria = Criteria::create();

        $criteria->where($criteria->expr()->eq('ssReserve', false))
            ->andWhere($criteria->expr()->eq('lostReplacement', false))
            ->orderBy(['fromNo' => Criteria::ASC]);

        $ranges = $this->getIrhpPermitRanges()->matching($criteria);

        if (is_null($emissionsCategoryId)) {
            return $ranges;
        }

        $filteredRanges = new ArrayCollection();
        foreach ($ranges as $range) {
            if ($range->getEmissionsCategory()->getId() == $emissionsCategoryId) {
                $filteredRanges->add($range);
            }
        }

        return $filteredRanges;
    }

    /**
     * Get the validity year of this stock
     *
     * @return int
     */
    public function getValidityYear()
    {
        return is_null($this->getValidTo(true)) ? null : $this->getValidTo(true)->format('Y');
    }

    /**
     * Does stock have a Euro5 Range?
     *
     * @return bool
     */
    public function hasEuro5Range()
    {
        return($this->hasRangeWithEmissionsCat(RefData::EMISSIONS_CATEGORY_EURO5_REF));
    }

    /**
     * Does stock have a Euro6 Range?
     *
     * @return bool
     */
    public function hasEuro6Range()
    {
        return($this->hasRangeWithEmissionsCat(RefData::EMISSIONS_CATEGORY_EURO6_REF));
    }

    /**
     * Does stock have a range with given emissions category ref data id?
     *
     * @param string $emissionsRef
     * @return bool
     */
    protected function hasRangeWithEmissionsCat(string $emissionsRef)
    {
        $ranges = $this->getIrhpPermitRanges();
        foreach ($ranges as $range) {
            if ($range->getEmissionsCategory()->getId() == $emissionsRef) {
                return true;
            }
        }

        return false;
    }
}
