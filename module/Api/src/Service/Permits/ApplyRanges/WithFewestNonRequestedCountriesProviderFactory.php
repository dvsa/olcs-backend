<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WithFewestNonRequestedCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return WithFewestNonRequestedCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithFewestNonRequestedCountriesProvider(
            $serviceLocator->get('PermitsApplyRangesRestrictedCountryIdsProvider')
        );
    }
}
