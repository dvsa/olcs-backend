<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Rbac;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use ZfcRbac\Identity\IdentityProviderInterface;
use ZfcRbac\Options\ModuleOptions;

class IdentityProviderFactory implements FactoryInterface
{
    const MESSAGE_CONFIG_MISSING = 'Missing auth.identity_provider from config';
    const MESSAGE_UNABLE_TO_CREATE = 'Unable to create requested identity provider';
    const MESSAGE_DOES_NOT_IMPLEMENT = 'Requested Identity Provider does not implement: ' . IdentityProviderInterface::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return IdentityProviderInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IdentityProviderInterface
    {
        $identityProvider = $container->get('config')['auth']['identity_provider'] ?? '';
        if (empty($identityProvider)) {
            throw new RunTimeException(static::MESSAGE_CONFIG_MISSING);
        }

        if (!$container->has($identityProvider)) {
            throw new RunTimeException(static::MESSAGE_UNABLE_TO_CREATE);
        }

        $instance = $container->get($identityProvider);

        if (!$instance instanceof IdentityProviderInterface) {
            throw new RunTimeException(static::MESSAGE_DOES_NOT_IMPLEMENT);
        }
        return $instance;
    }

    /**
     * @deprecated remove following Laminas v3 upgrade
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IdentityProviderInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IdentityProviderInterface
    {
        return $this($serviceLocator, null);
    }
}
