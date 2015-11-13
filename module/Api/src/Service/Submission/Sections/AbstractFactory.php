<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

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
        $mainSl = $serviceLocator->getServiceLocator();
        $config = $mainSl->get('Config')['submissions']['sections']['aliases'];
        $className = $config[$requestedName];
        $viewRenderer = $mainSl->get('viewrenderer');

        return new $className($mainSl->get('QueryHandlerManager'), $viewRenderer);
    }
}
