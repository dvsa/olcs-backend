<?php

namespace Dvsa\Olcs\Api\Mvc;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $listener = new OlcsBlameableListener($container);
        $listener->setServiceLocator($container);

        return $listener;
    }
}
