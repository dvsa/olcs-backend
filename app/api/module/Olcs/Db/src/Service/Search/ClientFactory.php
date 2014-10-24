<?php

namespace Olcs\Db\Service\Search;

use Elastica\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;

class ClientFactory implements FactoryInterface
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
        if (!isset($config['elastic_search'])) {
            throw new Exception\RuntimeException('Elastic search config not found');
        }

        $service = new Client($config['elastic_search']);

        return $service;
    }
}
