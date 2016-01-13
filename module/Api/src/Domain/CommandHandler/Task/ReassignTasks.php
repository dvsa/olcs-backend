<?php

/**
 * Reassign Tasks
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Task;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\Task\ReassignTasks as Cmd;

/**
 * Reassign Tasks
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ReassignTasks extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Task';

    /**
     * @param Cmd $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $userId = $command->getUser();
        if (empty($userId)) {
            $user = null;
        } else {
            $user = $this->getRepo()->getReference(User::class, $command->getUser());
        }

        $team = $this->getRepo()->getReference(Team::class, $command->getTeam());

        foreach ($command->getIds() as $id) {

            /** @var Task $task */
            $task = $this->getRepo()->fetchById($id);
            $task->setAssignedToUser($user);
            $task->setAssignedToTeam($team);
            $this->getRepo()->save($task);
        }

        $result->addMessage(count($command->getIds()) . ' Task(s) reassigned');

        return $result;
    }
}
