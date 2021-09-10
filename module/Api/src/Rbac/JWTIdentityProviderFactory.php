<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Olcs\Auth\Service\AuthenticationService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see JWTIdentityProvider
 */
class JWTIdentityProviderFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return JWTIdentityProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): JWTIdentityProvider
    {
        return new JWTIdentityProvider(
            $container->get('RepositoryServiceManager')->get('User'),
            $container->get('Request'),
            $container->get(Client::class)
        );
    }


    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return JWTIdentityProvider
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): JWTIdentityProvider
    {
        return $this->__invoke($serviceLocator, JWTIdentityProvider::class);
    }
}
