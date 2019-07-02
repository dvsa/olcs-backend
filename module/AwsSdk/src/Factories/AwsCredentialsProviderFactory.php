<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\InstanceProfileProvider;
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
        $credentialsProvider = CredentialProvider::instanceProfile();
        $provider = CredentialProvider::memoize($credentialsProvider);
        return $provider;
    }
}
