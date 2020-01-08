<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Sqs\SqsClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $sqsClient = new SqsClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $serviceLocator->get('AwsCredentialsProvider'),
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
