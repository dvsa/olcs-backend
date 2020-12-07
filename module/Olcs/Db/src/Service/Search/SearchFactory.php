<?php

namespace Olcs\Db\Service\Search;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

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
        $service->setAuthService($serviceLocator->get(AuthorizationService::class));

        return $service;
    }
}
