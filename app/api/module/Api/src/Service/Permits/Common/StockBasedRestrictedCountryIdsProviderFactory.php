<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StockBasedRestrictedCountryIdsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockBasedRestrictedCountryIdsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockBasedRestrictedCountryIdsProvider(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $serviceLocator->get('PermitsCommonTypeBasedRestrictedCountriesProvider')
        );
    }
}
