<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class QaEntityProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QaEntityProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QaEntityProvider
    {
        return $this->__invoke($serviceLocator, QaEntityProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return QaEntityProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QaEntityProvider
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new QaEntityProvider(
            $repoServiceManager->get('IrhpApplication'),
            $repoServiceManager->get('IrhpPermitApplication')
        );
    }
}
