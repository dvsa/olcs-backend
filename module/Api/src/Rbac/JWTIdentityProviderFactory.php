<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Authentication\Cognito\Client;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @see JWTIdentityProvider
 */
class JWTIdentityProviderFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return JWTIdentityProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): JWTIdentityProvider
    {
        return new JWTIdentityProvider(
            $container->get('RepositoryServiceManager')->get('User'),
            $container->get('Request'),
            $container->get(Client::class)
        );
    }
}
