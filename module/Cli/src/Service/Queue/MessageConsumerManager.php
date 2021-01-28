<?php

/**
 * Message Consumer Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManager
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;

/**
 * Message Consumer Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MessageConsumerManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }

        $this->addInitializer(
            new ServiceLocatorInitializer()
        );
    }

    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof MessageConsumerInterface) {
            throw new RuntimeException('Message consumer service does not implement MessageConsumerInterface');
        }
    }
}
