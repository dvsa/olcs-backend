<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StockAvailabilityCounterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockAvailabilityCounter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockAvailabilityCounter(
            $serviceLocator->get('PermitsAvailabilityEmissionsCategoryAvailabilityCounter')
        );
    }
}
