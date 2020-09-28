<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
