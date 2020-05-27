<?php

/**
 * Update User
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update User Last Login At
 */
final class UpdateUserLastLoginAt extends AbstractUserCommandHandler
{
    protected $repoServiceName = 'User';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\User $repo */
        $repo = $this->getRepo();

        $users = $repo->fetchByLoginId($command->getId());

        if (count($users) != 1) {
            // TODO: Remove and throw exception
            var_dump("ERROR HANDLING COMMAND");
            var_dump($users);
            die();
        }

        /** @var User $user */
        $user = $users[0];

        $user->setLastLoginAt(new \DateTime());

        $repo->save($user);

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User last login at updated successfully');

        return $result;
    }
}
