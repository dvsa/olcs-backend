<?php

namespace Dvsa\Olcs\Api\Service\EventHistory;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;
use Interop\Container\ContainerInterface;

class CreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Creator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Creator
    {
        return $this->__invoke($serviceLocator, Creator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Creator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Creator
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new Creator(
            $container->get(AuthorizationService::class),
            $repoServiceManager->get('EventHistory'),
            $repoServiceManager->get('EventHistoryType')
        );
    }
}
