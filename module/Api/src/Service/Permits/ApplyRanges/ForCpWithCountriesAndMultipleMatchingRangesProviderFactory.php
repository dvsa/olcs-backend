<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForCpWithCountriesAndMultipleMatchingRangesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForCpWithCountriesAndMultipleMatchingRangesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ForCpWithCountriesAndMultipleMatchingRangesProvider(
            $serviceLocator->get('PermitsApplyRangesWithFewestNonRequestedCountriesProvider')
        );
    }
}
