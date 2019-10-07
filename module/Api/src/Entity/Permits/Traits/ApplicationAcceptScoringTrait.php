<?php

namespace Dvsa\Olcs\Api\Entity\Permits\Traits;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Application Accept Scoring
 */
trait ApplicationAcceptScoringTrait
{
    /**
     * Indicate the type of notification required upon completion of scoring acceptance, returning one of the
     * NOTIFICATION_TYPE_* constants
     *
     * @return string
     */
    public function getOutcomeNotificationType()
    {
        $mappings = [
            IrhpInterface::SOURCE_SELFSERVE => ApplicationAcceptConsts::NOTIFICATION_TYPE_EMAIL,
            IrhpInterface::SOURCE_INTERNAL => ApplicationAcceptConsts::NOTIFICATION_TYPE_MANUAL
        ];

        return $mappings[$this->source->getId()];
    }

    /**
     * Indicate whether this application is either unsuccessful, partially successful or fully successful, returning
     * one of the class constants SUCCESS_LEVEL_NONE, SUCCESS_LEVEL_PARTIAL or SUCCESS_LEVEL_FULL accordingly
     *
     * @return string
     */
    public function getSuccessLevel()
    {
        $permitsAwarded = $this->getPermitsAwarded();

        $successLevel = ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL;
        if ($permitsAwarded == 0) {
            $successLevel = ApplicationAcceptConsts::SUCCESS_LEVEL_NONE;
        } elseif ($this->calculateTotalPermitsRequired() == $permitsAwarded) {
            $successLevel = ApplicationAcceptConsts::SUCCESS_LEVEL_FULL;
        }

        return $successLevel;
    }

    /**
     * Proceeds the application from under consideration to unsuccessful during the accept scoring process
     *
     * @param RefData $unsuccessfulStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToUnsuccessful(RefData $unsuccessfulStatus)
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to unsuccessful ('.$this->status->getId().')');
        }

        $this->status = $unsuccessfulStatus;
    }

    /**
     * Proceeds the application from under consideration to awaiting fee during the accept scoring process
     *
     * @param RefData $awaitingFeeStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAwaitingFee(RefData $awaitingFeeStatus)
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to awaiting fee ('.$this->status->getId().')');
        }

        $this->status = $awaitingFeeStatus;
    }

    /**
     * Return the number of permits awarded to this application. Only applicable to applications that are currently
     * under consideration
     *
     * @return int
     *
     * @throws ForbiddenException
     */
    public function getPermitsAwarded()
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException(
                'This application is not in the correct state to return permits awarded ('.$this->status->getId().')'
            );
        }

        return $this->getFirstIrhpPermitApplication()->countPermitsAwarded();
    }
}
