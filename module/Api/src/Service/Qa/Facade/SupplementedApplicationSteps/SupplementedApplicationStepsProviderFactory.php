<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SupplementedApplicationStepsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SupplementedApplicationStepsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SupplementedApplicationStepsProvider
    {
        return $this->__invoke($serviceLocator, SupplementedApplicationStepsProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SupplementedApplicationStepsProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplementedApplicationStepsProvider
    {
        return new SupplementedApplicationStepsProvider(
            $container->get('FormControlServiceManager'),
            $container->get('QaSupplementedApplicationStepFactory')
        );
    }
}
