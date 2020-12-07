<?php

namespace Dvsa\Olcs\Api\Service\Lva\Application;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;

/**
 * PublishValidationService
 *
 * Publish application validation
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PublishValidationService implements \Laminas\ServiceManager\FactoryInterface
{
    const ERROR_MUST_COMPETE_OC = 'APP-PUB-OC';
    const ERROR_MUST_COMPETE_TM = 'APP-PUB-TM';
    const ERROR_OUSTANDING_FEE = 'APP-PUB-OUSTANDING-FEE';
    const ERROR_NOT_PUBLISHABLE = 'APP-PUB-NOT-PUBLISHABLE';

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->feesHelper = $serviceLocator->get('FeesHelperService');

        return $this;
    }

    /**
     * Validate the application for publishing
     *
     * @param ApplicationEntity $application
     *
     * @return array of validation error messages
     */
    public function validate(ApplicationEntity $application)
    {
        $errors = [];

        $applicationCompletion = $application->getApplicationCompletion();

        // The status of the operating centre section is NOT complete; AND/OR
        if ($applicationCompletion->getOperatingCentresStatus() !== ApplicationCompletion::STATUS_COMPLETE) {
            $errors[self::ERROR_MUST_COMPETE_OC] = 'Must complete Operating Centres';
        }

        // The application licence type is standard national or international and
        // the transport manager section is NOT complete
        if (($application->isStandardNational() || $application->isStandardInternational()) &&
            $applicationCompletion->getTransportManagersStatus() !== ApplicationCompletion::STATUS_COMPLETE) {
            $errors[self::ERROR_MUST_COMPETE_TM] = 'Must complete Transport Managers';
        }

        // There is an outstanding application fee;
        if (!empty($this->feesHelper->getOutstandingFeesForApplication($application->getId(), true))) {
            $errors[self::ERROR_OUSTANDING_FEE] = 'There is an outstanding application fee';
        }

        if (!$application->isPublishable()) {
            $errors[self::ERROR_NOT_PUBLISHABLE] = 'Application is not publishable';
        }

        return $errors;
    }
}
