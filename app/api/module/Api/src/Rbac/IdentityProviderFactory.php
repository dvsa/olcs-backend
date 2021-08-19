<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Rbac;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Options\ModuleOptions;

class IdentityProviderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return IdentityProviderInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IdentityProviderInterface
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        assert($moduleOptions instanceof ModuleOptions);

        $identityProvider = $container->get($moduleOptions->getIdentityProvider());
        assert($identityProvider instanceof IdentityProviderInterface);

        return $identityProvider;
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
        return $this($serviceLocator, IdentityProviderInterface::class);
    }
}
