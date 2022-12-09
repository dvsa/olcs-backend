<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class WindowAvailabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return WindowAvailabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator): WindowAvailabilityChecker
    {
        return $this->__invoke($serviceLocator, WindowAvailabilityChecker::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return WindowAvailabilityChecker
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WindowAvailabilityChecker
    {
        return new WindowAvailabilityChecker(
            $container->get('RepositoryServiceManager')->get('IrhpPermitWindow'),
            $container->get('PermitsAvailabilityStockAvailabilityChecker')
        );
    }
}
