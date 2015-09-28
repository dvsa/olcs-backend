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
class PublishValidationService implements \Zend\ServiceManager\FactoryInterface
{
    const ERROR_MUST_COMPETE_OC = 'APP-PUB-OC';
    const ERROR_MUST_COMPETE_TM = 'APP-PUB-TM';
    const ERROR_OUSTANDING_FEE = 'APP-PUB-OUSTANDING-FEE';
    const ERROR_S4 = 'APP-PUB-S4';
    const ERROR_NOT_PUBLISHABLE = 'APP-PUB-NOT-PUBLISHABLE';

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
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
        if (!empty($this->feesHelper->getOutstandingFeesForApplication($application->getId()))) {
            $errors[self::ERROR_OUSTANDING_FEE] = 'There is an outstanding application fee';
        }

        // There is an schedule 4/1 record with statuses blank or Approved;
        if ($application->hasActiveS4()) {
            $errors[self::ERROR_S4] = 'There is an associated blank or approved S4';
        }

        if (!$application->isPublishable()) {
            $errors[self::ERROR_NOT_PUBLISHABLE] = 'Application is not publishable';
        }

        return $errors;
    }
}
