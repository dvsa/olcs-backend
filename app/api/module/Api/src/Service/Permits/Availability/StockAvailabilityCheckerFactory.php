<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StockAvailabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockAvailabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockAvailabilityChecker(
            $serviceLocator->get('PermitsAvailabilityStockAvailabilityCounter')
        );
    }
}
