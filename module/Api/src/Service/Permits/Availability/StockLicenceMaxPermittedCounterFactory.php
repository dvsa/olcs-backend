<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StockLicenceMaxPermittedCounterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockLicenceMaxPermittedCounter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockLicenceMaxPermittedCounter(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
