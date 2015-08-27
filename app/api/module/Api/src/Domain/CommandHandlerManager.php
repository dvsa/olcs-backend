<?php

/**
 * Command Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Command Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerManager extends AbstractPluginManager implements CommandHandlerInterface
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->setShareByDefault(false);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function handleCommand(CommandInterface $query)
    {
        return $this->get(get_class($query))->handleCommand($query);
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof CommandHandlerInterface)) {
            throw new RuntimeException('Command handler does not implement CommandHandlerInterface');
        }
    }
}
