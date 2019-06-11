<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForCpWithNoCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForCpWithNoCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ForCpWithNoCountriesProvider(
            $serviceLocator->get('PermitsApplyRangesUnrestrictedWithLowestStartNumberProvider'),
            $serviceLocator->get('PermitsApplyRangesRestrictedWithFewestCountriesProvider')
        );
    }
}
