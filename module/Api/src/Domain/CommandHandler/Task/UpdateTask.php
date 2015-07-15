<?php

/**
 * Update Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Task;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\Task\UpdateTask as Cmd;

/**
 * Update Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTask extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Task';

    /**
     * @param Cmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Task $task */
        $task = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $task->setDescription($command->getDescription());
        $task->setActionDate(new DateTime($command->getActionDate()));
        $task->setUrgent($command->getUrgent());
        $task->setCategory($this->getRepo()->getCategoryReference($command->getCategory()));
        $task->setSubCategory($this->getRepo()->getSubCategoryReference($command->getSubCategory()));

        $userId = $command->getAssignedToUser();
        if (empty($userId)) {
            $task->setAssignedToUser(null);
        } else {
            $task->setAssignedToUser($this->getRepo()->getReference(User::class, $command->getAssignedToUser()));
        }

        $task->setAssignedToTeam($this->getRepo()->getReference(Team::class, $command->getAssignedToTeam()));

        $this->getRepo()->save($task);

        $result->addMessage('Task updated');

        return $result;
    }
}
