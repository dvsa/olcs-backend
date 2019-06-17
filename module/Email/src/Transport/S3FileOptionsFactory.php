<?php

namespace Dvsa\Olcs\Email\Transport;

use Aws\S3\S3Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class S3FileOptionsFactory implements FactoryInterface
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
        $s3Client = $serviceLocator->get('S3Client');
        list($awsOptions, $s3Options, $bucket, $key) = $this->extractConfig($config);
        $s3fileOptions = new S3FileOptions([
            'awsOptions' => $awsOptions,
            's3Options' => $s3Options,
            's3Bucket' => $bucket,
            's3Key' => $key
        ], $s3Client);
        return $s3fileOptions;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function extractConfig(array $config): array
    {
        $awsOptions = $config['awsOptions'];
        $s3Options = $awsOptions['s3Options'];
        $arraykey = array_search(
            '\Dvsa\Olcs\Email\Transport\S3File',
            array_column($config['mail']['options']['transport'], 'type')
        );
        $bucket = $config['mail']['options']['transport'][$arraykey]['options']['bucket'];
        $key = $config['mail']['options']['transport'][$arraykey]['options']['key'];
        return array($awsOptions, $s3Options, $bucket, $key);
    }
}
