<?php

/**
 * Create a team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Team;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

/**
 * Create a team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateTeam extends AbstractCommandHandler implements AuthAwareInterface
{
    protected $repoServiceName = 'Team';

    use AuthAwareTrait;

    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $this->checkIfTeamAlreadyExists($command->getName());

        $team = new TeamEntity();
        $team->setName($command->getName());
        $team->setDescription($command->getDescription());
        if ($command->getTrafficArea()) {
            $team->setTrafficArea(
                $this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea())
            );
        }

        $this->getRepo()->save($team);

        $result = new Result();
        $result->addId('team', $team->getId());
        $result->addMessage('Team created successfully');

        return $result;
    }

    /**
     * Check whether a team with a such name already exist
     */
    protected function checkIfTeamAlreadyExists($name)
    {
        if (count($this->getRepo()->fetchByName($name))) {
            throw new ValidationException([TeamEntity::ERROR_TEAM_EXISTS => 'Team with such a name already exists']);
        }
    }
}
