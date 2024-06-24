<?php

namespace Dvsa\Olcs\Api\Service\AppRegistration\Adapter;

use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManagerInterface;
use Exception;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AppRegistrationSecretFactory implements FactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AppRegistrationSecret
    {
        $config = $container->get('config');

        if (!isset($config['app-registrations']['secrets']['provider'])) {
            throw new Exception('App registration secrets provider not configured');
        }

        $secretsProvider = is_subclass_of($config['app-registrations']['secrets']['provider'], SecretsManagerInterface::class)
            ? $config['app-registrations']['secrets']['provider']
            : LocalSecretsManager::class;

        return new AppRegistrationSecret($container->get($secretsProvider));
    }
}
