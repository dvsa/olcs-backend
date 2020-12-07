<?php

/**
 * Query Builder Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Query Builder Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new QueryBuilder($serviceLocator->get('QueryPartialServiceManager'));
    }
}
