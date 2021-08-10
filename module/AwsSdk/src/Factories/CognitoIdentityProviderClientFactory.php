<?php
declare(strict_types=1);

namespace Dvsa\Olcs\AwsSdk\Factories;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CognitoIdentityProviderClientFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CognitoIdentityProviderClient
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CognitoIdentityProviderClient
    {
        $config = $container->get('Config');
        $provider = $container->get('AwsCredentialsProvider');
        return  new CognitoIdentityProviderClient([
            'credentials' => $provider,
            'version' => '2016-04-18',
            'region' => $config['awsOptions']['cognito']['region'],
        ]);
    }

    /**
     * @inheritDoc
     * @deprecated Can be removed following Laminas v3 upgrade
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CognitoIdentityProviderClient
    {
        return $this->__invoke($serviceLocator, null);
    }
}
