<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Credentials\AssumeRoleCredentialProvider;
use Aws\Credentials\CredentialProvider;
use Aws\Sts\StsClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AwsCredentailsProviderFactory
 * @author shaun.hare@dvsa.gov.uk
 * @package Dvsa\Olcs\AwsSdk
 */
class AwsCredentialsProviderFactory implements FactoryInterface
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
        $assumeRoleCredentials = new AssumeRoleCredentialProvider([
            'client' => new StsClient([
                'region' => $config['awsOptions']['region'],
                'version' => $config['awsOptions']['version']
            ]),
            'assume_role_params' => [
                'RoleArn' => $config['awsOptions']['s3Options']['roleArn'],
                'RoleSessionName' => $config['awsOptions']['s3Options']['roleSessionName'],
            ]
        ]);

        $provider = CredentialProvider::memoize($assumeRoleCredentials);
        return $provider;
    }
}
