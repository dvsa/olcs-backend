<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RangeBasedRestrictedCountriesProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RangeBasedRestrictedCountriesProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RangeBasedRestrictedCountriesProvider
    {
        return $this->__invoke($serviceLocator, RangeBasedRestrictedCountriesProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RangeBasedRestrictedCountriesProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RangeBasedRestrictedCountriesProvider
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new RangeBasedRestrictedCountriesProvider(
            $repoServiceManager->get('IrhpPermitRange'),
            $container->get('PermitsCommonTypeBasedPermitTypeConfigProvider'),
            $repoServiceManager->get('Country')
        );
    }
}
