<?php

/**
 * Update Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
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
final class UpdateBusinessDetails extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        try {

            $this->getRepo()->beginTransaction();

            $updateResult = $this->updateBusinessDetails($command);

            $result->merge($updateResult);

            if ($updateResult->getFlag('hasChanged')) {
                $result->merge($this->updateApplicationCompletion($command));
            }

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function updateBusinessDetails(Cmd $command)
    {
        $data = $command->getArrayCopy();

        $data['id'] = $data['licence'];

        return $this->getCommandHandler()->handleCommand(LicenceCmd::create($data));
    }

    private function updateApplicationCompletion(Cmd $command)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateApplicationCompletion::create(['id' => $command->getId(), 'section' => 'businessDetails'])
        );
    }
}
