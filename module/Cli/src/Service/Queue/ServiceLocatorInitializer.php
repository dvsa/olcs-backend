<?php

namespace Dvsa\Olcs\Cli\Service\Queue;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\InitializerInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServiceLocatorInitializer
 *
 * @package Dvsa\Olcs\Cli\Service\Queue
 */
class ServiceLocatorInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param mixed $instance
     *
     * return mixed
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($container->getServiceLocator());
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, $instance);
    }
}
