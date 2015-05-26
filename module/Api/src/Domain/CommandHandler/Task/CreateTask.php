<?php

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Task;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as Cmd;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateTask extends AbstractCommandHandler
{
    protected $repoServiceName = 'Task';

    public function handleCommand(CommandInterface $command)
    {
        $task = $this->createTaskObject($command);

        $this->getRepo()->save($task);

        $result = new Result();
        $result->addId('task', $task->getId());
        $result->addMessage('Task created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Task
     */
    private function createTaskObject(Cmd $command)
    {
        // Required
        $category = $this->getRepo()->getCategoryReference($command->getCategory());
        $subCategory = $this->getRepo()->getSubCategoryReference($command->getSubCategory());

        $task = new Task($category, $subCategory);

        // Optional relationships
        if ($command->getAssignedToUser() !== null) {
            $assignedToUser = $this->getRepo()->getReference(User::class, $command->getAssignedToUser());
            $task->setAssignedToUser($assignedToUser);
        }

        if ($command->getAssignedToTeam() !== null) {
            $assignedToTeam = $this->getRepo()->getReference(Team::class, $command->getAssignedToTeam());
            $task->setAssignedToTeam($assignedToTeam);
        }

        if ($command->getApplication() !== null) {
            $application = $this->getRepo()->getReference(Application::class, $command->getApplication());
            $task->setApplication($application);
        }

        if ($command->getLicence() !== null) {
            $Licence = $this->getRepo()->getReference(Licence::class, $command->getLicence());
            $task->setLicence($Licence);
        }

        if ($command->getActionDate() !== null) {
            $task->setActionDate(new \DateTime($command->getActionDate()));
        }

        // Task properties
        $task->setDescription($command->getDescription());
        $task->setIsClosed($command->getIsClosed());
        $task->setUrgent($command->getUrgent());

        return $task;
    }
}
