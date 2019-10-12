<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RangeBasedRestrictedCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RangeBasedRestrictedCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new RangeBasedRestrictedCountriesProvider(
            $repoServiceManager->get('IrhpPermitRange'),
            $serviceLocator->get('PermitsCommonTypeBasedRestrictedCountriesProvider'),
            $repoServiceManager->get('Country')
        );
    }
}
