<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('PermitsCommonStockBasedPermitTypeConfigProvider')
        );
    }
}
