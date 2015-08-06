<?php

namespace Dvsa\Olcs\Api\Service\Submission\Process;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Submission\Process
 */
class PluginManager extends AbstractPluginManager
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
        if (!($plugin instanceof ProcessInterface)) {
            throw new Exception\RuntimeException(get_class($plugin) . ' should implement: ' . ProcessInterface::class);
        }
    }
}
