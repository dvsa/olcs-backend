<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class WindowAvailabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return WindowAvailabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WindowAvailabilityChecker(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitWindow'),
            $serviceLocator->get('PermitsAvailabilityStockAvailabilityChecker')
        );
    }
}
