<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class SectionGeneratorPluginManager extends AbstractPluginManager
{
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
        // if (!($plugin instanceof SectionGeneratorInterface)) {
        //     throw new Exception\RuntimeException(
        //         get_class($plugin) . ' should implement: ' . SectionGeneratorInterface::class
        //     );
        // }
    }
}
