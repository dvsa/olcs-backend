<?php

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface CommandHandlerInterface
{
    public function handleCommand(CommandInterface $command);
}
