<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\S3\S3Client;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class S3ClientFactory
 *
 * @package Dvsa\Olcs\AwsSdk\Factories
 */
class S3ClientFactory implements FactoryInterface
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
        $provider = $serviceLocator->get('AwsCredentialsProvider');
        $s3Client = new S3Client([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]);
        /**
         * @var S3Client
         */
        return $s3Client;
    }
}
