<?php

namespace Dvsa\Olcs\Cli\Service\Queue;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;

/**
 * Message Consumer Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MessageConsumerManager extends AbstractPluginManager
{
    protected $instanceOf = MessageConsumerInterface::class;
}
