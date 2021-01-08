<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractFactory
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class AbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return in_array(AbstractContext::class, class_parents($requestedName), true);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mainSl = $container->getServiceLocator();
        return new $requestedName($mainSl->get('QueryHandlerManager'));
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }
}
