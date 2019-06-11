<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForCpWithCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForCpWithCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ForCpWithCountriesProvider(
            $serviceLocator->get('PermitsApplyRangesRestrictedWithMostMatchingCountriesProvider'),
            $serviceLocator->get('PermitsApplyRangesForCpWithCountriesAndNoMatchingRangesProvider'),
            $serviceLocator->get('PermitsApplyRangesForCpWithCountriesAndMultipleMatchingRangesProvider')
        );
    }
}
