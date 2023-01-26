<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StockBasedPermitTypeConfigProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockBasedPermitTypeConfigProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StockBasedPermitTypeConfigProvider
    {
        return $this->__invoke($serviceLocator, StockBasedPermitTypeConfigProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StockBasedPermitTypeConfigProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StockBasedPermitTypeConfigProvider
    {
        return new StockBasedPermitTypeConfigProvider(
            $container->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $container->get('PermitsCommonTypeBasedPermitTypeConfigProvider')
        );
    }
}
