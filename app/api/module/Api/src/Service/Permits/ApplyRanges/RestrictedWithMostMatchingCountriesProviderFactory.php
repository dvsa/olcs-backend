<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RestrictedWithMostMatchingCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedWithMostMatchingCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RestrictedWithMostMatchingCountriesProvider(
            $serviceLocator->get('PermitsApplyRangesRestrictedRangesProvider')
        );
    }
}
