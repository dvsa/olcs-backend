<?php

/**
 * Abstract Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service;

use Interop\Container\Containerinterface;
use Laminas\ServiceManager\Config;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Abstract Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractServiceManagerFactory extends AbstractPluginManagerFactory
{
    const CONFIG_KEY = 'define_me';

    protected $serviceManagerClass;

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('Config');

        $configObject = new Config(!empty($config[static::CONFIG_KEY]) ? $config[static::CONFIG_KEY] : []);

        $class = $this->serviceManagerClass;

        $plugins = new $class($configObject);
        $plugins->setService($container);

        return $plugins;
    }
}
