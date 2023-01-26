<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ValidatableAdapterFactory implements FactoryInterface
{
    const CONFIG_NAMESPACE = 'auth';
    const AUTH_CONFIG_DEFAULT_ADAPTER = 'default_adapter';
    const AUTH_CONFIG_ADAPTERS = 'adapters';
    const ADAPTER_CONFIG_ADAPTER = 'adapter';

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ValidatableAdapterInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidatableAdapterInterface
    {
        $config = $container->get('config');
        $adapterConfig = $this->getAdapterConfig($config);
        $adapterClass = $adapterConfig[static::ADAPTER_CONFIG_ADAPTER];

        if (!is_string($adapterClass)) {
            $adapter = $adapterClass;
        } elseif ($container->has($adapterClass)) {
            $adapter = $container->get($adapterClass);
        } else {
            $adapter = new $adapterClass($adapterConfig);
        }

        if (! $adapter instanceof ValidatableAdapterInterface) {
            throw new \InvalidArgumentException('Defined adapter is not instance of ' . ValidatableAdapterInterface::class);
        }

        return $adapter;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @deprecated Can be removed following Laminas v3 upgrade
     * @return ValidatableAdapterInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ValidatableAdapterInterface
    {
        return $this->__invoke($serviceLocator, null);
    }


    /**
     * Returns the configuration from the default adapter
     *
     * @param array $config
     * @return array
     */
    protected function getAdapterConfig(array $config): array
    {
        if (! array_key_exists(static::CONFIG_NAMESPACE, $config)) {
            throw new \InvalidArgumentException('Config namespace is not defined: ' . static::CONFIG_NAMESPACE);
        }

        $defaultAdapter = $config[static::CONFIG_NAMESPACE][static::AUTH_CONFIG_DEFAULT_ADAPTER] ?? null;
        if (null === $defaultAdapter) {
            throw new \InvalidArgumentException('Default adapter not defined: ' . static::AUTH_CONFIG_DEFAULT_ADAPTER);
        }

        $adapterConfig = $config[static::CONFIG_NAMESPACE][static::AUTH_CONFIG_ADAPTERS][$defaultAdapter] ?? null;
        if (null === $adapterConfig) {
            throw new \InvalidArgumentException('Missing config for default adapter: ' . $defaultAdapter);
        }

        if (! array_key_exists(static::ADAPTER_CONFIG_ADAPTER, $adapterConfig)) {
            throw new \InvalidArgumentException('Adaptor class is not defined in the adapter configuration for adaptor: ' . $defaultAdapter);
        }

        return $adapterConfig;
    }
}
