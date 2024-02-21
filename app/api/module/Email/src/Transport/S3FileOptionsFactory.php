<?php

namespace Dvsa\Olcs\Email\Transport;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class S3FileOptionsFactory implements FactoryInterface
{
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

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return S3FileOptions
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): S3FileOptions
    {
        $config = $container->get('Config');
        $s3Client = $container->get('S3Client');
        list($awsOptions, $s3Options, $bucket, $key) = $this->extractConfig($config);
        $s3fileOptions = new S3FileOptions([
            'awsOptions' => $awsOptions,
            's3Options' => $s3Options,
            's3Bucket' => $bucket,
            's3Key' => $key
        ], $s3Client);
        return $s3fileOptions;
    }
}
