<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see ChangePasswordFactory
 */
class ChangePasswordFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ChangePassword
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ChangePassword
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ChangePassword
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $instance = new ChangePassword($adapter);
        return $instance->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ChangePassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): ChangePassword
    {
        return $this($serviceLocator, ChangePassword::class);
    }
}
