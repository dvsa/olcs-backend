<?php

namespace Dvsa\Olcs\Api\Service\AppRegistration\Adapter;

use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManagerInterface;
use Exception;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AppRegistrationSecretsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return AppRegistrationSecret
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AppRegistrationSecret
    {
        return $this->__invoke($serviceLocator, AppRegistrationSecret::class);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AppRegistrationSecret
    {
        $config = $container->get('Config');

        if (!isset($config['app-registrations']['secrets']['provider'])) {
            throw new Exception('App registration secrets provider not configured');
        }

        $secretsProvider = is_subclass_of($config['app-registrations']['secrets']['provider'], SecretsManagerInterface::class)
            ? $config['app-registrations']['secrets']['provider']
            : LocalSecretsManager::class;

        return new AppRegistrationSecret($container->get($secretsProvider));
    }
}
