<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see RefreshToken
 */
class RefreshTokenFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return RefreshToken
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefreshToken
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $instance = new RefreshToken($adapter);
        return $instance->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RefreshToken
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RefreshToken
    {
        return $this($serviceLocator, RefreshToken::class);
    }
}
