<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Class SectionGeneratorPluginManagerFactory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class SectionGeneratorPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = SectionGeneratorPluginManager::class;
}
