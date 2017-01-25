<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete User
 */
final class DeleteUser extends AbstractCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    OpenAmUserAwareInterface
{
    use AuthAwareTrait,
        OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task', 'OrganisationUser'];

    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $user = $this->getRepo()->fetchUsingId($command);

        if (!empty($this->getRepo('Task')->fetchByUser($user->getId(), true))) {
            // the user still has some open tasks
            throw new BadRequestException('ERR_USER_HAS_OPEN_TASK');
        }

        $this->getRepo('OrganisationUser')->deleteByUserId($user->getId());
        $this->getRepo()->delete($user);

        $this->getOpenAmUser()->disableUser(
            $user->getPid()
        );

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User deleted successfully');

        return $result;
    }
}
