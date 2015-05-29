<?php

/**
 * Query Builder Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
