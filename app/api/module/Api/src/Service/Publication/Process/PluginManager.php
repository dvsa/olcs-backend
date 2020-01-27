<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Process
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = ProcessInterface::class;

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
