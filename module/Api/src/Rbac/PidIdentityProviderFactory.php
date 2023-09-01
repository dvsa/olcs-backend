<?php

namespace Dvsa\Olcs\Api\Rbac;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * @see PidIdentityProvider
 */
class PidIdentityProviderFactory implements FactoryInterface
{
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
