<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class PluginManager extends AbstractPluginManager
{
    public function __construct(ContainerInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addAbstractFactory(new AbstractFactory());
        $this->addInitializer(array($this, 'injectAddressFormatter'), false);
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        // TODO - OLCS-26007
        // if (!($plugin instanceof ContextInterface)) {
        //     throw new Exception\RuntimeException(get_class($plugin) . ' should implement: ' . ContextInterface::class);
        // }
    }

    public function injectAddressFormatter($service, ServiceLocatorInterface $serviceLocator)
    {
        if ($service instanceof AddressFormatterAwareInterface) {
            $parentLocator = $serviceLocator->getServiceLocator();
            $service->setAddressFormatter($parentLocator->get('AddressFormatter'));
        }

        return $service;
    }
}
