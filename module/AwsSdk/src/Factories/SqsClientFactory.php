<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Sqs\SqsClient;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class S3ClientFactory
 *
 * @package Dvsa\Olcs\AwsSdk\Factories
 */
class SqsClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SqsClient
    {
        return $this->__invoke($serviceLocator, SqsClient::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SqsClient
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SqsClient
    {
        $config = $container->get('Config');
        $sqsClient = new SqsClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'http'    => [
                'proxy' => $config['companies_house_connection']['proxy']
            ]
        ]);
        /**
         * @var SqsClient
         */
        return $sqsClient;
    }
}
