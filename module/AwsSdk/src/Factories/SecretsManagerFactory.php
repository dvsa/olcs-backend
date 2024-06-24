<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Exception;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class SecretsManagerFactory implements FactoryInterface
{
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
        $config = $container->get('config');

        // get the cache
        $cache = $container->get(CacheEncryption::class);

        $client = new SecretsManagerClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
        ]);

        return new SecretsManager($client, $cache);
    }
}
