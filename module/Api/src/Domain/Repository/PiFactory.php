<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PiFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        return new Pi(
            $sm->get('doctrine.entitymanager.orm_default'),
            $sm->get('QueryBuilder'),
            $serviceLocator->get('Cases')
        );
    }
}
