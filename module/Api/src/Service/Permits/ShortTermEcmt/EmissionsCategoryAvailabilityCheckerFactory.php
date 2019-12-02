<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsCategoryAvailabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryAvailabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsCategoryAvailabilityChecker(
            $serviceLocator->get('PermitsShortTermEcmtEmissionsCategoryAvailabilityCounter')
        );
    }
}
