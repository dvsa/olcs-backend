<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client as RestClient;
use Interop\Container\ContainerInterface;

/**
 * Class InrClientFactory
 * @package Dvsa\Olcs\Api\Service\Nr
 */
class InrClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return InrClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InrClient
    {
        return $this->__invoke($serviceLocator, InrClient::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return InrClient
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): InrClient
    {
        $config = $container->get('Config');
        if (!isset($config['nr']['inr_service'])) {
            throw new \RuntimeException('Missing INR service config');
        }
        $httpClient = new RestClient($config['nr']['inr_service']['uri']);
        $httpClient->setAdapter($config['nr']['inr_service']['adapter']);
        $httpClient->getAdapter()->setOptions($config['nr']['inr_service']['options']);
        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        return new InrClient($httpClient);
    }
}
