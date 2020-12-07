<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TypeBasedPermitTypeConfigProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TypeBasedPermitTypeConfigProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TypeBasedPermitTypeConfigProvider(
            $serviceLocator->get('Config')
        );
    }
}
