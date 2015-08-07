<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginManagerFactory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = PluginManager::class;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $sectionConfig = $serviceLocator->get('Config')['submissions']['sections'];

        $config = new ServiceManagerConfig($sectionConfig);
        $config->configureServiceManager($service);

        return $service;
    }
}
