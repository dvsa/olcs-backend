<?php

namespace Olcs\Db\Service\Search;

use Elastica\Client;
use Interop\Container\ContainerInterface;
use Olcs\Logging\Log\Logger;
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
     * @throws \Zend\ServiceManager\Exception\InvalidServiceException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        if (!isset($config['elastic_search'])) {
            throw new Exception\InvalidServiceException('Elastic search config not found');
        }

        $service = new Client($config['elastic_search']);

        if (isset($config['elastic_search']['log'])) {
            $log = new ZendLogPsr3Adapter(Logger::getLogger());
            $service->setLogger($log);
        }

        return $service;
    }
}
