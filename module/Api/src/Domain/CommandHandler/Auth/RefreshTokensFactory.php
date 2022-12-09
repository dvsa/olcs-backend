<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see RefreshTokens
 */
class RefreshTokensFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RefreshTokens
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefreshTokens
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $instance = new RefreshTokens($adapter);
        return $instance->createService($container);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RefreshTokens
     *@deprecated Remove once Laminas v3 upgrade is complete
     *
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): RefreshTokens
    {
        return $this($serviceLocator, RefreshTokens::class);
    }
}
