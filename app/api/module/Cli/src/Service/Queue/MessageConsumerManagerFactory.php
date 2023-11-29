<?php

namespace Dvsa\Olcs\Cli\Service\Queue;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

class MessageConsumerManagerFactory extends AbstractServiceManagerFactory
{
    const PLUGIN_MANAGER_CLASS = MessageConsumerManager::class;
    const CONFIG_KEY = 'message_consumer_manager';
}
