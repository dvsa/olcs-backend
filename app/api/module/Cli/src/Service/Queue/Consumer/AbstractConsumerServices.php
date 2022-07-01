<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;

/**
 * Abstract Consumer Services
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AbstractConsumerServices
{
    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /**
     * Create service instance
     *
     * @param CommandHandlerManager $commandHandlerManager
     *
     * @return AbstractConsumerServices
     */
    public function __construct(CommandHandlerManager $commandHandlerManager)
    {
        $this->commandHandlerManager = $commandHandlerManager;
    }

    /**
     * Return the command handler manager service
     *
     * @return CommandHandlerManager
     */
    public function getCommandHandlerManager(): CommandHandlerManager
    {
        return $this->commandHandlerManager;
    }
}
