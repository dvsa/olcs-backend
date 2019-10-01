<?php

namespace Dvsa\Olcs\Api\Entity\Permits\Traits;

use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Application Accept Scoring interface
 */
interface ApplicationAcceptScoringInterface
{
    /**
     * Get the id of the application
     *
     * @return int
     */
    public function getId();

    /**
     * Indicate the type of notification required upon completion of scoring acceptance, returning one of the
     * NOTIFICATION_TYPE_* constants
     *
     * @return string
     */
    public function getOutcomeNotificationType();

    /**
     * Indicate whether this application is either unsuccessful, partially successful or fully successful, returning
     * one of the class constants SUCCESS_LEVEL_NONE, SUCCESS_LEVEL_PARTIAL or SUCCESS_LEVEL_FULL accordingly
     *
     * @return string
     */
    public function getSuccessLevel();

    /**
     * Proceeds the application from under consideration to unsuccessful during the accept scoring process
     *
     * @param RefData $unsuccessfulStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToUnsuccessful(RefData $unsuccessfulStatus);

    /**
     * Proceeds the application from under consideration to awaiting fee during the accept scoring process
     *
     * @param RefData $awaitingFeeStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAwaitingFee(RefData $awaitingFeeStatus);

    /**
     * Return the number of permits awarded to this application. Only applicable to applications that are currently
     * under consideration
     *
     * @return int
     *
     * @throws ForbiddenException
     */
    public function getPermitsAwarded();

    /**
     * Return the entity name in camel case
     *
     * @return string
     */
    public function getCamelCaseEntityName();

    /**
     * Return an array of mappings between success levels and email commands
     *
     * @return array
     */
    public function getEmailCommandLookup();

    /**
     * Return the product reference to be used for the issue fee
     *
     * @return string
     */
    public function getIssueFeeProductReference();
}
