<?php

namespace Dvsa\Olcs\AcquiredRights\Service;

use Dvsa\Olcs\AcquiredRights\Client\AcquiredRightsClient;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Olcs\Logging\Log\Logger;
use phpDocumentor\Reflection\Types\Boolean;

class AcquiredRightsServiceFactory implements FactoryInterface
{
    protected const CONFIG_NAMESPACE = 'acquired_rights';
    protected const CONFIG_KEY_EXPIRY = 'expiry';
    protected const CONFIG_KEY_CHECK_ENABLED = 'check_enabled';

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AcquiredRightsService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AcquiredRightsService
    {
        if ($container instanceof ServiceLocatorAwareInterface) {
            $container = $container->getServiceLocator();
        }

        $logger = $container->get('Logger');
        $acquiredRightsExpiry = $this->getAcquiredRightsExpiryFromConfig($container->get('Config'));
        $acquiredRightsCheckEnabled = $this->getAcquiredRightsCheckEnabledFromConfig($container->get('Config'));
        $acquiredRightsClient = $container->get(AcquiredRightsClient::class);

        return new AcquiredRightsService($logger, $acquiredRightsClient, $acquiredRightsExpiry, $acquiredRightsCheckEnabled);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return AcquiredRightsService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @deprecated Can be removed following Laminas v3 upgrade
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AcquiredRightsService
    {
        return $this->__invoke($serviceLocator, AcquiredRightsService::class);
    }

    /**
     * @param array $config
     * @return \DateTimeImmutable
     */
    protected function getAcquiredRightsExpiryFromConfig(array $config): \DateTimeImmutable
    {
        $acquiredRightsExpiry = $config[static::CONFIG_NAMESPACE][static::CONFIG_KEY_EXPIRY] ?? null;
        if (empty($acquiredRightsExpiry)) {
            $errorMsg = sprintf(
                'Configuration is undefined or empty(): %s -> %s',
                static::CONFIG_NAMESPACE,
                static::CONFIG_KEY_EXPIRY
            );
            Logger::err($errorMsg);
            throw new \InvalidArgumentException($errorMsg);
        }
        if (!$acquiredRightsExpiry instanceof \DateTimeImmutable) {
            $errorMsg = sprintf(
                'Value must be instance of \DateTimeImmutable: %s -> %s',
                static::CONFIG_NAMESPACE,
                static::CONFIG_KEY_EXPIRY
            );
            Logger::err($errorMsg);
            throw new \InvalidArgumentException($errorMsg);
        }
        return $acquiredRightsExpiry;
    }

    /**
     * @param array $config
     * @return bool
     */
    protected function getAcquiredRightsCheckEnabledFromConfig(array $config): bool
    {
        $acquiredRightsCheckEnabled = $config[static::CONFIG_NAMESPACE][static::CONFIG_KEY_CHECK_ENABLED] ?? null;
        if (is_null($acquiredRightsCheckEnabled)) {
            $errorMsg = sprintf(
                'Configuration is undefined or null: %s -> %s',
                static::CONFIG_NAMESPACE,
                static::CONFIG_KEY_CHECK_ENABLED
            );
            Logger::err($errorMsg);
            throw new \InvalidArgumentException($errorMsg);
        }
        if (!is_bool($acquiredRightsCheckEnabled)) {
            $errorMsg = sprintf(
                'Value must be instance of bool: %s -> %s',
                static::CONFIG_NAMESPACE,
                static::CONFIG_KEY_CHECK_ENABLED
            );
            Logger::err($errorMsg);
            throw new \InvalidArgumentException($errorMsg);
        }
        return $acquiredRightsCheckEnabled;
    }
}
