<?php

/**
 * Delete a team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Team;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;

/**
 * Delete a team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class DeleteTeam extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Team';

    protected $extraRepos = ['User', 'Task'];

    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $team = $this->getRepo()->fetchUsingId($command);
        $this->validateTeam($team);

        $result = new Result();
        // need to reassign tasks but don't have a new team
        if (count($team->getTasks()) && !$command->getNewTeam()) {
            $result->addId('tasks', count($team->getTasks()));
            $result->addMessage('Need to reassign ' . count($team->getTasks()) . ' task(s)');
            return $result;
        }

        // dry run, just need to validate if we can remove the team
        if ($command->getValidate()) {
            $result->addMessage('Ready to remove');
            return $result;
        }

        // base functionality - reassign tasks and remove the team
        if ($command->getNewTeam()) {
            $result->addMessage(count($team->getTasks()) . ' task(s) reassigned');
            $this->reassignTasks($team, $command->getNewTeam());
        }
        $this->getRepo()->delete($team);
        $result->addId('team', $team->getId());
        $result->addMessage('Team deleted successfully');
        return $result;
    }

    protected function validateTeam($team)
    {
        $errors = [];
        $usersCount = $this->getRepo('User')->fetchUsersCountByTeam($team->getId());
        if ($usersCount) {
            $errors[TeamEntity::ERROR_TEAM_LINKED_TO_USERS] = '- It is linked to user records(s)';
        }
        if (count($team->getTaskAllocationRules())) {
            $errors[TeamEntity::ERROR_TEAM_LINKED_TO_TASK_ALLOCATION_RULES] = '- It is used to allocate tasks';
        }
        if (count($team->getPrinters())) {
            $errors[TeamEntity::ERROR_TEAM_LINKED_TO_PRINTER_SETTINGS] =
                '- It is used by printing (see administration page)';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    protected function reassignTasks($team, $newTeamId)
    {
        $newTeam = $this->getRepo()->fetchById($newTeamId);

        $tasks = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($team->getTasks() as $task) {
            $task->setAssignedToTeam($newTeam);
            $tasks->add($task);
        }
        $newTeam->addTasks($tasks);
        $this->getRepo()->save($newTeam);

        $team->setTasks([]);
        $this->getRepo()->save($team);
    }
}
