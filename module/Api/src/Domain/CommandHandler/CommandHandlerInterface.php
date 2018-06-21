<?php

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface CommandHandlerInterface
{
    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command);

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
