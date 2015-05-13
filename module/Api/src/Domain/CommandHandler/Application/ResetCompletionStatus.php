<?php

/**
 * Reset Completion Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetCompletionStatus as Cmd;

/**
 * Reset Completion Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetCompletionStatus extends AbstractCommandHandler
{
    public function handleCommand(CommandInterface $command)
    {
        throw new \Exception('TODO: Implement this');
    }
}
