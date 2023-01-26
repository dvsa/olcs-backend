<?php

namespace Dvsa\Olcs\Api\Rbac;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * @see PidIdentityProvider
 */
class PidIdentityProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PidIdentityProvider
    {
        return $this->__invoke($serviceLocator, PidIdentityProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PidIdentityProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PidIdentityProvider
    {
        $config = $container->get('Config');
        return new PidIdentityProvider(
            $container->get('RepositoryServiceManager')->get('User'),
            $container->get('Request'),
            isset($config['openam']['pid_header']) ? $config['openam']['pid_header'] : 'X-Pid',
            $config['auth']['adapters']['openam']['cookie']['name']
        );
    }
}
