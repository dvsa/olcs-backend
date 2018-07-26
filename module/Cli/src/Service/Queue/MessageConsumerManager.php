<?php

/**
 * Message Consumer Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue\MessageConsumerManager
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        $this->addInitializer(array($this, 'initialize'));
    }

    public function initialize($instance)
    {
        if ($instance instanceof ServiceLocatorInterface) {
            $instance->creationContext = $this->getServiceLocator();
        }
    }

    public function validate($plugin)
    {
        if (!$plugin instanceof MessageConsumerInterface) {
            throw new InvalidServiceException('Message consumer service does not implement MessageConsumerInterface');
        }
    }

    public function getServiceLocator()
    {
        return $this->creationContext;
    }

    public function setServiceLocator(ContainerInterface $container)
    {
        $this->creationContext = $container;
    }
}
