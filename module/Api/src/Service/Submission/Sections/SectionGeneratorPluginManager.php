<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Interop\Container\ContainerInterface;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class SectionGeneratorPluginManager extends AbstractPluginManager
{
    public function __construct(ContainerInterface $configuration = null)
    {
        parent::__construct($configuration);
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws InvalidServiceException if invalid
     */
    public function validate($plugin)
    {
        if (!($plugin instanceof ContainerInterface)) {
            throw new InvalidServiceException(
                get_class($plugin) . ' should implement: ' . ContainerInterface::class
            );
        }
    }
}
