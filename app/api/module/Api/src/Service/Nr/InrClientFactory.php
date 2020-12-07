<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client as RestClient;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

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
