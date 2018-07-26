<?php

/**
 * Entity Manager Helper Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\EntityManager;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Entity Manager Helper Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EntityManagerHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new EntityManagerHelper();
        $service->setEntityManager($container->get('doctrine.entitymanager.orm_default'));

        return $service;
    }
}
