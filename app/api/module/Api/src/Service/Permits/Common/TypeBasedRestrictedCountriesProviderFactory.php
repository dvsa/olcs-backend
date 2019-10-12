<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TypeBasedRestrictedCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TypeBasedRestrictedCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TypeBasedRestrictedCountriesProvider(
            $serviceLocator->get('Config')
        );
    }
}
