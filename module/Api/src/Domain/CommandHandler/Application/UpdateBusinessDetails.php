<?php

/**
 * Update Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Application\UpdateBusinessDetails as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateBusinessDetails as LicenceCmd;

/**
 * Update Business Details
 * @NOTE This handler basically calls the licence version, but then adds the update application completion side effect
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateBusinessDetails extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $updateResult = $this->updateBusinessDetails($command);
        $this->result->merge($updateResult);
        $this->result->merge($this->updateApplicationCompletion($command, $updateResult->getFlag('hasChanged')));

        return $this->result;
    }

    private function updateBusinessDetails(Cmd $command)
    {
        $data = $command->getArrayCopy();

        $data['id'] = $data['licence'];

        return $this->handleSideEffect(LicenceCmd::create($data));
    }

    private function updateApplicationCompletion(Cmd $command, $hasChanged)
    {
        return $this->handleSideEffect(
            UpdateApplicationCompletionCommand::create(
                [
                    'id' => $command->getId(),
                    'section' => 'businessDetails',
                    'data' => [
                        'hasChanged' => $hasChanged
                    ]
                ]
            )
        );
    }
}
