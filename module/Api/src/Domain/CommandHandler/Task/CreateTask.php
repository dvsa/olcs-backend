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
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule;

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateTask extends AbstractCommandHandler
{
    protected $repoServiceName = 'Task';

    protected $extraRepos = ['TaskAllocationRule', 'SystemParameter'];

    public function handleCommand(CommandInterface $command)
    {
        $task = $this->createTaskObject($command);

        if ($task->getAssignedToUser() === null) {
            $this->autoAssignTask($task);
        }

        $this->getRepo()->save($task);

        $result = new Result();
        $result->addId('task', $task->getId());
        $result->addMessage('Task created successfully');

        return $result;
    }

    private function autoAssignTask(Task $task)
    {
        $ruleType = $task->getCategory()->getTaskAllocationType()->getId();

        switch ($ruleType) {
            case Task::TYPE_SIMPLE:
                $rules = $this->getRepo('TaskAllocationRule')
                    ->fetchForSimpleTaskAssignment($task->getCategory());
                break;
            // no other allocation type is yet implemented as of OLCS-3406
            case Task::TYPE_MEDIUM:
            case Task::TYPE_COMPLEX:
            default:
                return;
        }

        /**
         * Multiple rules are just as useless as no rules according to AC
         */
        if (count($rules) !== 1) {
            return $this->assignToDefault($task);
        }

        /** @var TaskAllocationRule $rule */
        $rule = $rules[0];

        $task->setAssignedToUser($rule->getUser());
        $task->setAssignedToTeam($rule->getTeam());
    }

    /**
     * Fall back on system configuration to populate user and team
     *
     * @return array
     */
    private function assignToDefault(Task $task)
    {
        $teamId = $this->getRepo('SystemParameter')->fetchValue('task.default_team');
        $userId = $this->getRepo('SystemParameter')->fetchValue('task.default_user');

        if ($teamId !== null) {
            $task->setAssignedToTeam($this->getRepo()->getReference(Team::class, $teamId));
        }

        if ($userId !== null) {
            $task->setAssignedToUser($this->getRepo()->getReference(User::class, $userId));
        }
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
