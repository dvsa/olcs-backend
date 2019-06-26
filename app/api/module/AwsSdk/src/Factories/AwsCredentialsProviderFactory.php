<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Credentials\AssumeRoleCredentialProvider;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\InstanceProfileProvider;
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


        $credentialsProvidor = CredentialProvider::instanceProfile();
        $provider = CredentialProvider::memoize($credentialsProvidor);
        return $provider;
    }

    private function getSpecificRole( array $config, $role)
    {

        $profile = new InstanceProfileProvider();
        return new AssumeRoleCredentialProvider([
            'client' => new StsClient([
                'region' => $config['awsOptions']['region'],
                'version' => $config['awsOptions']['version'],
                'credentials' => $profile
            ]),
            'assume_role_params' => [
                'RoleArn' => $config['awsOptions'][$role]['roleArn'],
                'RoleSessionName' => $config['awsOptions'][$role]['roleSessionName'],
            ]
        ]);
    }
}
