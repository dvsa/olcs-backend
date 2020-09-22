<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
