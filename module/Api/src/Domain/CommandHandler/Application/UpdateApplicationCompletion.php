<?php

/**
 * Update Application Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as Cmd;

/**
 * Update Application Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateApplicationCompletion extends AbstractCommandHandler
{
    public function handleCommand(CommandInterface $command)
    {
        throw new \Exception('TODO: Implement this');
    }
}
