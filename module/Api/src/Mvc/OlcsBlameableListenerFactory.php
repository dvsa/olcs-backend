<?php

namespace Dvsa\Olcs\Api\Mvc;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class OlcsBlameableListenerFactory
 *
 * @package Olcs\Api\Mvc
 */
class OlcsBlameableListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listener = new OlcsBlameableListener($serviceLocator);
        $listener->setServiceLocator($serviceLocator);

        return $listener;
    }
}
