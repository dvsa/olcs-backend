<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractFactory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class AbstractFactory implements FactoryInterface
{
    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $name, $requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config')['submissions']['sections']['aliases'];
        $className = $config[$requestedName];
        $viewRenderer = $container->get('viewrenderer');

        return new $className($container->get('QueryHandlerManager'), $viewRenderer);
    }
}
