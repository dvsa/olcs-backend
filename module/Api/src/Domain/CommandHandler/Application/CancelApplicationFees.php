<?php

/**
 * Cancel Application Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\CancelApplicationFees as Cmd;

/**
 * Cancel Application Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CancelApplicationFees extends AbstractCommandHandler
{
    public function handleCommand(CommandInterface $command)
    {
        throw new \Exception('TODO: Implement this');
    }
}
