<?php

/**
 * Service factory - Creates instances of services and injects dependencies
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory - Creates instances of services and injects dependencies
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Factory implements FactoryInterface
{
    private $container;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->container = $container;

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

        $namespaces = $this->container->get('Config')['entity_namespaces'];

        if ($setEntityName) {
            $service->setEntityName('\Dvsa\Olcs\Api\Entity\\' . $namespaces[$name] . '\\' . $name);
        }

        $service->setEntityManager($this->container->get('doctrine.entitymanager.orm_default'));
        $service->setServiceLocator($this->container);

        return $service;
    }

    public function getServiceClassName($name)
    {
        return '\Olcs\Db\Service\\' . $name;
    }
}
