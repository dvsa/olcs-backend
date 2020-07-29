<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
