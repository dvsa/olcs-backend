<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TypeBasedPermitTypeConfigProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TypeBasedPermitTypeConfigProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TypeBasedPermitTypeConfigProvider
    {
        return $this->__invoke($serviceLocator, TypeBasedPermitTypeConfigProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TypeBasedPermitTypeConfigProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TypeBasedPermitTypeConfigProvider
    {
        return new TypeBasedPermitTypeConfigProvider(
            $container->get('Config')
        );
    }
}
