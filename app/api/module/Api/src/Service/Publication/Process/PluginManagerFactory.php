<?php


namespace Dvsa\Olcs\Api\Service\Publication\Process;


use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginManagerFactory
 * @package Dvsa\Olcs\Api\Service\Publication\Process
 */
class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = PluginManager::class;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $handlers = $serviceLocator->get('Config')['publication_process'];
        $config = new ServiceManagerConfig($handlers);
        $config->configureServiceManager($service);

        return $service;
    }
}
