<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Authentication\Cognito\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CognitoAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CognitoAdapter
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CognitoAdapter
    {
        $client = $container->get(Client::class);

        return new CognitoAdapter($client);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @deprecated Can be removed following Laminas v3 upgrade
     * @return CognitoAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CognitoAdapter
    {
        return $this->__invoke($serviceLocator, null);
    }
}
