<?php

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Interop\Container\ContainerInterface;
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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $class = __NAMESPACE__ . '\\' . $requestedName;

        $repo = new $class(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('QueryBuilder'),
            $container->get('DbQueryServiceManager')
        );

        $repo->initService($container);

        return $repo;
    }
}
