<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CandidatePermitsGrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsGrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CandidatePermitsGrantabilityChecker(
            $serviceLocator->get('PermitsAvailabilityCandidatePermitsAvailableCountCalculator')
        );
    }
}
