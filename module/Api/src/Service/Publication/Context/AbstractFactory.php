<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractFactory
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class AbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return in_array(AbstractContext::class, class_parents($requestedName), true);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $mainSl = $serviceLocator->getServiceLocator();
        return new $requestedName($mainSl->get('QueryHandlerManager'));
    }
}
