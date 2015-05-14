<?php

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as Cmd;

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplicationFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        try {
            $this->getRepo()->beginTransaction();

            $task = null;

            if ($this->isGranted('internal-view')) {
                // Create task
                $taskResult = $this->getCommandHandler()->handleCommand($this->createCreateTaskCommand($command));
                $result->merge($taskResult);

                $task = $taskResult->getId('task');
            }

            $result->merge($this->getCommandHandler()->handleCommand($this->createCreateFeeCommand($command, $task)));

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    private function createCreateTaskCommand(Cmd $command)
    {
        return CreateTask::create(['id' => $command->getId()]);
    }

    private function createCreateFeeCommand(Cmd $command, $task)
    {
        return CreateFee::create(['id' => $command->getId()]);
    }

    /**
     * @TODO Need to replace this with a real way to determine between internal and selfserve users
     *
     * @param $permission
     * @return bool
     */
    private function isGranted($permission)
    {
        return true;
    }
}
