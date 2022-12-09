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
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CognitoAdapter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CognitoAdapter
    {
        $client = $container->get(Client::class);

        return new CognitoAdapter($client);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return CognitoAdapter
     * @deprecated Can be removed following Laminas v3 upgrade
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CognitoAdapter
    {
        return $this->__invoke($serviceLocator, null);
    }
}
