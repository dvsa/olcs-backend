<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RestrictedWithFewestCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedWithFewestCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RestrictedWithFewestCountriesProvider(
            $serviceLocator->get('PermitsApplyRangesRestrictedRangesProvider')
        );
    }
}
