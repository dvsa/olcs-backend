<?php

/**
 * Abstract Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service;

use Zend\ServiceManager\Config;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractServiceManagerFactory extends AbstractPluginManagerFactory
{
    const CONFIG_KEY = 'define_me';

    protected $serviceManagerClass;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $configObject = new Config(!empty($config[static::CONFIG_KEY]) ? $config[static::CONFIG_KEY] : []);

        $class = $this->serviceManagerClass;

        $plugins = new $class($serviceLocator);
        $plugins->configure($configObject->toArray());

        return $plugins;
    }
}
