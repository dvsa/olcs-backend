<?php

namespace Olcs\Db\Service\Search;

use ElasticSearch\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Class SearchFactory
 * @package Olcs\Db\Service\Search
 */
class SearchFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Search
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Search
    {
        return new Search(
            $container->get(Client::class),
            $container->get(AuthorizationService::class),
            $container->get('RepositoryServiceManager')->get('SystemParameter')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Search
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Search
    {
        return $this->__invoke($serviceLocator, null);
    }
}
