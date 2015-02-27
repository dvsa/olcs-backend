<?php

namespace Olcs\Db\Service\Search;

use Elastica\Client;
use Olcs\Logging\Log\ZendLogPsr3Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;

/**
 * Class ClientFactory
 * @package Olcs\Db\Service\Search
 */
class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @throws \Zend\ServiceManager\Exception\RuntimeException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['elastic_search'])) {
            throw new Exception\RuntimeException('Elastic search config not found');
        }

        $service = new Client($config['elastic_search']);

        if (isset($config['elastic_search']['log'])) {
            $log = new ZendLogPsr3Adapter($serviceLocator->get('Logger'));
            $service->setLogger($log);
        }

        return $service;
    }
}
