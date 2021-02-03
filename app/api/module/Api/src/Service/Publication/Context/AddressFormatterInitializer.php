<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\InitializerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AddressFormatterInitializer
 *
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class AddressFormatterInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param mixed $instance
     *
     * return mixed
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof AddressFormatterAwareInterface) {
            $parentLocator = $container->getServiceLocator();
            $instance->setAddressFormatter($parentLocator->get('AddressFormatter'));
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
