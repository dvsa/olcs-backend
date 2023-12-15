<?php

namespace Dvsa\Olcs\Api\Service;

use Interop\Container\Containerinterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

abstract class AbstractServiceManagerFactory extends AbstractPluginManagerFactory
{
    public const CONFIG_KEY = 'define_me';

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('Config');
        $configArray = !empty($config[static::CONFIG_KEY]) ? $config[static::CONFIG_KEY] : [];

        return parent::__invoke($container, static::PLUGIN_MANAGER_CLASS, $configArray);
    }
}
