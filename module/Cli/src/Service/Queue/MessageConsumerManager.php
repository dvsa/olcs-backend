<?php

/**
 * Message Consumer Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManager
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
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
    use PluginManagerTrait;

    protected $instanceOf = MessageConsumerInterface::class;

    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }

        $this->addInitializer(
            new ServiceLocatorInitializer()
        );
    }
}
