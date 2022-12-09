<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationFeesClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationFeesClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationFeesClearer
    {
        return $this->__invoke($serviceLocator, ApplicationFeesClearer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationFeesClearer
     */
public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationFeesClearer
    {
        return new ApplicationFeesClearer(
            $container->get('CqrsCommandCreator'),
            $container->get('CommandHandlerManager'),
            $container->get('RepositoryServiceManager')->get('Fee')
        );
    }
}
