<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as RestClient;

/**
 * Class InrClientFactory
 * @package Dvsa\Olcs\Api\Service\Nr
 */
class InrClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nr']['inr_service'])) {
            throw new \RuntimeException('Missing INR service config');
        }

        $httpClient = new RestClient($config['nr']['inr_service']['uri'], $config['nr']['inr_service']['options']);

        return new InrClient($httpClient);
    }
}
