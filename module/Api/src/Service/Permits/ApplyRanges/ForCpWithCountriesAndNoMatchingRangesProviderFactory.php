<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForCpWithCountriesAndNoMatchingRangesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForCpWithCountriesAndNoMatchingRangesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ForCpWithCountriesAndNoMatchingRangesProvider(
            $serviceLocator->get('PermitsApplyRangesUnrestrictedWithLowestStartNumberProvider'),
            $serviceLocator->get('PermitsApplyRangesRestrictedWithFewestCountriesProvider')
        );
    }
}
