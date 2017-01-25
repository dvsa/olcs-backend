<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete User Selfserve
 */
final class DeleteUserSelfserve extends AbstractCommandHandler implements
    TransactionedInterface,
    OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task', 'OrganisationUser'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $user = $this->getRepo()->fetchUsingId($command);

        if (!empty($this->getRepo('Task')->fetchByUser($user->getId(), true))) {
            // the user still has some open tasks
            throw new BadRequestException('The user still has some open tasks');
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
