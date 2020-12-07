<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('PermitsCommonTypeBasedPermitTypeConfigProvider'),
            $repoServiceManager->get('Country')
        );
    }
}
