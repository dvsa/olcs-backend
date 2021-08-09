<?php
declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Auth\Service\AuthenticationServiceInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth\LoginFactoryTest;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class LoginFactory
 * @see LoginFactoryTest
 */
class LoginFactory implements FactoryInterface
{
    const CONFIG_NAMESPACE = 'auth';
    const AUTH_CONFIG_DEFAULT_ADAPTER = 'default_adapter';
    const AUTH_CONFIG_ADAPTERS = 'adapters';
    const ADAPTER_CONFIG_ADAPTER = 'adapter';

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Login
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Login
    {
        assert($container instanceof ServiceLocatorAwareInterface);
        $pluginManager = $container;
        $container = $container->getServiceLocator();

        $adapter = $container->get(ValidatableAdapterInterface::class);
        $authService = $container->get(AuthenticationServiceInterface::class);
        $instance = new Login($authService, $adapter);
        return $instance->createService($pluginManager);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Login
     * @deprecated Use __invoke
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Login
    {
        return $this->__invoke($serviceLocator, null);
    }
}
