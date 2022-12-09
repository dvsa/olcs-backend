<?php

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\Credentials\CredentialProvider;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class AwsCredentailsProviderFactory
 *
 * @author  shaun.hare@dvsa.gov.uk
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
        return $this->__invoke($serviceLocator, CredentialProvider::class);
    }

    protected function getEnvCredentialProvider()
    {
        return CredentialProvider::env();
    }

    protected function getInstanceProfileCredentialProvider()
    {
        return CredentialProvider::instanceProfile();
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $envCredentialsFlag = $container->get('Config')['awsOptions']['useEnvCredentials'] ?? false;
        $credentialsProvider = $envCredentialsFlag ? $this->getEnvCredentialProvider() : $this->getInstanceProfileCredentialProvider();
        $provider = CredentialProvider::memoize($credentialsProvider);
        return $provider;
    }
}
