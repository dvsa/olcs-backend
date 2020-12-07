<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RefDataSourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RefDataSource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RefDataSource(
            $serviceLocator->get('RepositoryServiceManager')->get('RefData')
        );
    }
}
