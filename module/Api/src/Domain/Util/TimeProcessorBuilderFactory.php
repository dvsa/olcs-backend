<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class TimeProcessorBuilderFactory
 */
class TimeProcessorBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TimeProcessorBuilder
    {
        return $this->__invoke($serviceLocator, TimeProcessorBuilder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TimeProcessorBuilder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TimeProcessorBuilder
    {
        return new TimeProcessorBuilder($container->get('RepositoryServiceManager')->get('PublicHoliday'));
    }
}
