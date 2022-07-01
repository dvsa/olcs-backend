<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Context
 */
class PluginManager extends AbstractPluginManager
{
    use PluginManagerTrait;

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
}
