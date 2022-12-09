<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class DatabaseTwigLoaderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DatabaseTwigLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DatabaseTwigLoader
    {
        return $this->__invoke($serviceLocator, DatabaseTwigLoader::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DatabaseTwigLoader
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DatabaseTwigLoader
    {
        return new DatabaseTwigLoader(
            $container->get('TemplateDatabaseTemplateFetcher')
        );
    }
}
