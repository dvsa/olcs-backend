<?php

namespace Dvsa\Olcs\Api\Service\Permits\Allocate;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IrhpPermitAllocatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitAllocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpPermitAllocator
    {
        return $this->__invoke($serviceLocator, IrhpPermitAllocator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpPermitAllocator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IrhpPermitAllocator
    {
        return new IrhpPermitAllocator(
            $container->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
