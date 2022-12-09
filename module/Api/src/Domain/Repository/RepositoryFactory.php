<?php

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
        return $this->__invoke($serviceLocator,  $requestedName);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $class = __NAMESPACE__ . '\\' . $requestedName;
        $sm = $container->getServiceLocator();
        $repo = new $class(
            $sm->get('doctrine.entitymanager.orm_default'),
            $sm->get('QueryBuilder'),
            $sm->get('DbQueryServiceManager')
        );
        $repo->initService($container);
        return $repo;
    }
}
