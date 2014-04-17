<?php

/**
 * Service factory - Creates instances of services and injects dependencies
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory - Creates instances of services and injects dependencies
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Factory implements FactoryInterface
{
    private $serviceLocator;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    public function getService($name)
    {
        $className = $this->getServiceClassName($name);

        $setEntityName = false;

        if (!class_exists($className)) {

            $className = $this->getServiceClassName('Generic');
            $setEntityName = true;
        }

        $service = new $className();

        if ($setEntityName) {

            $service->setEntityName('\OlcsEntities\Entity\\' . $name);
        }

        $service->setEntityManager($this->serviceLocator->get('doctrine.entitymanager.orm_default'));
        $service->setServiceLocator($this->serviceLocator);

        return $service;
    }

    public function getServiceClassName($name)
    {
        return '\Olcs\Db\Service\\' . $name;
    }
}
