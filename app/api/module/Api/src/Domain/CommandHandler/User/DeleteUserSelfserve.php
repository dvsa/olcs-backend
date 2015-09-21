<?php

/**
 * Delete User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete User Selfserve
 */
final class DeleteUserSelfserve extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task'];

    public function handleCommand(CommandInterface $command)
    {
        // TODO - OLCS-10516 - User management restrictions

        $user = $this->getRepo()->fetchUsingId($command);

        if (!empty($this->getRepo('Task')->fetchByUser($user->getId(), true))) {
            // the user still has some open tasks
            throw new BadRequestException('The user still has some open tasks');
        }

        $this->getRepo()->delete($user);

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User deleted successfully');

        return $result;
    }
}
