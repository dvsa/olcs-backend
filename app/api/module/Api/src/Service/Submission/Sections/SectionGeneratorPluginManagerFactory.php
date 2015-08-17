<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SectionGeneratorPluginManagerFactory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class SectionGeneratorPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = SectionGeneratorPluginManager::class;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $sectionConfig = $serviceLocator->get('Config')['submissions']['sections'];

        $config = new ServiceManagerConfig($sectionConfig);
        $config->configureServiceManager($service);

        return $service;
    }
}
