<?php

/**
 * Delete User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete User Selfserve
 */
final class DeleteUserSelfserve extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task'];

    public function handleCommand(CommandInterface $command)
    {
        $user = $this->getRepo()->fetchUsingId($command);

        if (!$this->isGranted(Permission::CAN_MANAGE_USER_SELFSERVE, $user)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

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
