<?php

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Repository Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryFactory implements FactoryInterface
{
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
