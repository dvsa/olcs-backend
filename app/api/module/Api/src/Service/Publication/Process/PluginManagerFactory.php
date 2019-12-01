<?php
namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Class PluginManagerFactory
 * @package Dvsa\Olcs\Api\Service\Publication\Process
 */
class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = PluginManager::class;
}
