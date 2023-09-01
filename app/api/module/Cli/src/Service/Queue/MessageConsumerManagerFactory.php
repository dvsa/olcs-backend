<?php

/**
 * Message Consumer Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManagerFactory
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Interop\Container\Containerinterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Message Consumer Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManagerFactory
 */
class MessageConsumerManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = MessageConsumerManager::class;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return parent::__invoke($container, $config['message_consumer_manager']);
    }
}
