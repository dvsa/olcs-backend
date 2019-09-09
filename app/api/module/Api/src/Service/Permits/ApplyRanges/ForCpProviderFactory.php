<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForCpProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForCpProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ForCpProvider(
            $serviceLocator->get('PermitsApplyRangesForCpWithCountriesProvider'),
            $serviceLocator->get('PermitsApplyRangesForCpWithNoCountriesProvider'),
            $serviceLocator->get('PermitsApplyRangesEntityIdsExtractor'),
            $serviceLocator->get('PermitsApplyRangesRangeSubsetGenerator')
        );
    }
}
