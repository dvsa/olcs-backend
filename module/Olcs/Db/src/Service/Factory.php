<?php

namespace Olcs\Db\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        if (!class_exists($className)) {
            return false;
        }

        $service = new $className();
        $service->setEntityManager($this->serviceLocator->get('doctrine.entitymanager.orm_default'));
        $service->setServiceLocator($this->serviceLocator);

        return $service;
    }

    public function getServiceClassName($name)
    {
        return '\Olcs\Db\Service\\' . $name;
    }
}