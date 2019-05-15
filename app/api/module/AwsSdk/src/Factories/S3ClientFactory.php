<?php

namespace Dvsa\Olcs\AwsSdk;


use Aws\S3\S3Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $config = $serviceLocator->get('config');
        $provider = $serviceLocator->get('AWSCredentialsProvider');
        $s3Client = new S3Client([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]);
        return $s3Client;
    }
}