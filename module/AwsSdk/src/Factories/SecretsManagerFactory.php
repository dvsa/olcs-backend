<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Exception;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class SecretsManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SecretsManager
    {
        return $this->__invoke($serviceLocator, Secretsmanager::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SecretsManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SecretsManager
    {
        $config = $container->get('Config');


        // get the cache
        $cache = $container->get(CacheEncryption::class);

        $client = new SecretsManagerClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
        ]);

        return new SecretsManager($client, $cache);
    }
}
