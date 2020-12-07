<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = ContextInterface::class;

    public function __construct(ContainerInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addAbstractFactory(new AbstractFactory());
        $this->addInitializer(array($this, 'injectAddressFormatter'), false);
    }

    public function injectAddressFormatter($service, ServiceLocatorInterface $serviceLocator)
    {
        if ($service instanceof AddressFormatterAwareInterface) {
            $parentLocator = $serviceLocator->getServiceLocator();
            $service->setAddressFormatter($parentLocator->get('AddressFormatter'));
        }

        return $service;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($instance),
                $this->instanceOf
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
