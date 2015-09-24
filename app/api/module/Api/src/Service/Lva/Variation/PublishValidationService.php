<?php

namespace Dvsa\Olcs\Api\Service\Lva\Variation;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * PublishValidationService
 *
 * Publish variation validation
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PublishValidationService
{
    const ERROR_S4 = 'APP-PUB-S4';
    const ERROR_NOT_PUBLISHABLE = 'APP-PUB-NOT-PUBLISHABLE';

    /**
     * Validate the variation for publishing
     *
     * @param ApplicationEntity $application
     *
     * @return array of validation error messages
     */
    public function validate(ApplicationEntity $application)
    {
        $errors = [];

        // There is an schedule 4/1 record with statuses blank or Approved;
        if ($application->hasActiveS4()) {
            $errors[self::ERROR_S4] = 'There is an associated blank or approved S4';
        }
        if (!$application->isPublishable()) {
            $errors[self::ERROR_NOT_PUBLISHABLE] = 'Variation is not publishable';
        }

        return $errors;
    }
}
