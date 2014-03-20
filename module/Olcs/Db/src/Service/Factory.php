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
        $className = '\Olcs\Db\Service\\' . $name;

        $service = new $className();
        $service->setEntityManager($this->serviceLocator->get('doctrine.entitymanager.orm_default'));
        $service->setServiceLocator($this->serviceLocator);

        return $service;
    }
}