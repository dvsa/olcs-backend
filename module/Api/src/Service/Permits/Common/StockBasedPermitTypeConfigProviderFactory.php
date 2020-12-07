<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StockBasedPermitTypeConfigProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockBasedPermitTypeConfigProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockBasedPermitTypeConfigProvider(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $serviceLocator->get('PermitsCommonTypeBasedPermitTypeConfigProvider')
        );
    }
}
