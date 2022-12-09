<?php

namespace Dvsa\Olcs\Api\Service\Publication;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager as ContextPluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager as ProcessPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class PublicationGeneratorFactory
 * @package Dvsa\Olcs\Api\Service\Publication
 */
class PublicationGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PublicationGenerator
    {
        return $this->__invoke($serviceLocator, PublicationGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PublicationGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PublicationGenerator
    {
        return new PublicationGenerator(
            $container->get('Config')['publications'],
            $container->get(ContextPluginManager::class),
            $container->get(ProcessPluginManager::class)
        );
    }
}
