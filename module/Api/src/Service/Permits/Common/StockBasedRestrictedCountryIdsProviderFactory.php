<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StockBasedRestrictedCountryIdsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockBasedRestrictedCountryIdsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StockBasedRestrictedCountryIdsProvider
    {
        return $this->__invoke($serviceLocator, StockBasedRestrictedCountryIdsProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StockBasedRestrictedCountryIdsProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StockBasedRestrictedCountryIdsProvider
    {
        return new StockBasedRestrictedCountryIdsProvider(
            $container->get('PermitsCommonStockBasedPermitTypeConfigProvider')
        );
    }
}
