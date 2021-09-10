<?php

/**
 * Pid Identity Provider Factory
 */
namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Olcs\Auth\Service\AuthenticationService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see BlendedIdentityProvider
 */
class BlendedIdentityProviderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BlendedIdentityProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BlendedIdentityProvider
    {
        return new BlendedIdentityProvider(
            $container->get('Request'),
            $container->get(JWTIdentityProvider::class),
            $container->get(PidIdentityProvider::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return BlendedIdentityProvider
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BlendedIdentityProvider
    {
        return $this->__invoke($serviceLocator, BlendedIdentityProvider::class);
    }
}
