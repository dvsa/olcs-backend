<?php

/**
 * Entity Manager Helper Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\EntityManager;

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
        $service = new EntityManagerHelper();
        $service->setEntityManager($serviceLocator->get('doctrine.entitymanager.orm_default'));

        return $service;
    }
}
