<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\SecretsManager;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocalSecretsManagerFactory implements FactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LocalSecretsManager
    {
        return $this->__invoke($serviceLocator, LocalSecretsManager::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LocalSecretsManager
    {
        $config = $container->get('Config');
        return new LocalSecretsManager($config['localSecretsManager']);
    }
}
