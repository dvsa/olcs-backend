<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class DatabaseTemplateFetcherFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DatabaseTemplateFetcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DatabaseTemplateFetcher
    {
        return $this->__invoke($serviceLocator, DatabaseTemplateFetcher::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DatabaseTemplateFetcher
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DatabaseTemplateFetcher
    {
        return new DatabaseTemplateFetcher(
            $container->get('RepositoryServiceManager')->get('Template')
        );
    }
}
