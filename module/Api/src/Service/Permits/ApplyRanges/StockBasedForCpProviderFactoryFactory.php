<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StockBasedForCpProviderFactoryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockBasedForCpProviderFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockBasedForCpProviderFactory(
            $serviceLocator->get('PermitsCommonStockBasedRestrictedCountryIdsProvider'),
            $serviceLocator->get('PermitsApplyRangesForCpProviderFactory')
        );
    }
}
