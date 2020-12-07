<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EmissionsCategoriesGrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoriesGrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsCategoriesGrantabilityChecker(
            $serviceLocator->get('PermitsAvailabilityEmissionsCategoryAvailabilityCounter')
        );
    }
}
