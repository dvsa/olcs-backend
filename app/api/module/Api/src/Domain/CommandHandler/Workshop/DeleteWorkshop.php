<?php

/**
 * Delete Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Workshop;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteWorkshop extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Workshop';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $id) {
            $this->getRepo()->delete(
                $this->getRepo()->fetchById($id)
            );
        }

        $result->addMessage(count($command->getIds()) . ' Workshop(s) removed');

        return $result;
    }
}
