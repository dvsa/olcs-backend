<?php

/**
 * Message Consumer Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManagerFactory
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Laminas\ServiceManager\Config;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Message Consumer Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManagerFactory
 */
class MessageConsumerManagerFactory extends AbstractPluginManagerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configObject = new Config($config['message_consumer_manager']);

        $plugins = new MessageConsumerManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
