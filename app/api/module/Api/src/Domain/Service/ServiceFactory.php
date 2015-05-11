<?php

/**
 * Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ServiceFactory implements FactoryInterface
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

        return new $class($serviceLocator->getServiceLocator()->get('RepositoryServiceManager')->get($requestedName));
    }
}
