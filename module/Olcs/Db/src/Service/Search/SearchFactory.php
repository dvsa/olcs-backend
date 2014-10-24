<?php

namespace Olcs\Db\Service\Search;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SearchFactory
 * @package Olcs\Db\Service\Search
 */
class SearchFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Search();
        $service->setClient($serviceLocator->get('ElasticSearch\Client'));

        return $service;
    }
}
