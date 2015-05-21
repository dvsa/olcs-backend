<?php

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $class = __NAMESPACE__ . '\\' . $requestedName;
        $sm = $serviceLocator->getServiceLocator();

        return new $class($sm->get('doctrine.entitymanager.orm_default'), $sm->get('QueryBuilder'));
    }
}
