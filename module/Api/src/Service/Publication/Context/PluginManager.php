<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = ContextInterface::class;

    public function __construct(ContainerInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addAbstractFactory(new AbstractFactory());
        $this->addInitializer(
            new AddressFormatterInitializer(),
            false
        );
    }

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
