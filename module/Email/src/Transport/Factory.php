<?php

namespace Dvsa\Olcs\Email\Transport;

use Aws\Credentials\AssumeRoleCredentialProvider;
use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Aws\Sts\StsClient;
use Zend\Mail\Transport\Factory as ZendFactory;
use Zend\Mail\Transport\TransportInterface;

/**
 * Class Factory
 */
abstract class Factory extends ZendFactory
{
    /**
     * Factory create
     *
     * @param array $spec Spec for the transport
     *
     * @return TransportInterface
     */
    public static function create($spec = [])
    {
        $transport = parent::create($spec);

        if ($transport instanceof MultiTransport && isset($spec['options'])) {
            $transport->setOptions(new MultiTransportOptions($spec['options']));
        }
        if ($transport instanceof S3File && isset($spec['options'])) {

            $s3Client = self::getS3Client($spec);

            $transport->setOptions(new S3FileOptions($spec['options'], $s3Client));
        }

        return $transport;
    }

    /**
     * @param $spec
     *
     * @return S3Client
     */
    private static function getS3Client($spec): S3Client
    {
        $provider = self::getAwsCredentialProvider($spec);
        $s3Client = new S3Client([
            'region' => $spec['options']['awsOptions']['region'],
            'version' => $spec['options']['awsOptions']['version'],
            'credentials' => $provider
        ]);
        return $s3Client;
    }

    /**
     * @param $spec
     *
     * @return callable
     */
    private static function getAwsCredentialProvider($spec): callable
    {
        $assumeRoleCredentials = new AssumeRoleCredentialProvider([
            'client' => new StsClient([
                'region' => $spec['options']['awsOptions']['region'],
                'version' => $spec['options']['awsOptions']['version']
            ]),
            'assume_role_params' => [
                'RoleArn' => $spec['options']['s3Options']['roleArn'],
                'RoleSessionName' => $spec['options']['s3Options']['roleSessionName'],
            ]
        ]);

        $provider = CredentialProvider::memoize($assumeRoleCredentials);
        return $provider;
    }
}
