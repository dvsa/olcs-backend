<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CreateTaskCommandGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CreateTaskCommandGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CreateTaskCommandGenerator
    {
        return $this->__invoke($serviceLocator, CreateTaskCommandGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CreateTaskCommandGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CreateTaskCommandGenerator
    {
        return new CreateTaskCommandGenerator(
            $container->get('PermitsCheckableCreateTaskCommandFactory')
        );
    }
}
