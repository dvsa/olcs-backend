<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
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
    AuthAwareInterface,
    CacheAwareInterface,
    OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;
    use AuthAwareTrait;
    use CacheAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task', 'OrganisationUser'];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\User\DeleteUserSelfserve $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        if ((int)$command->getId() === $this->getCurrentUser()->getId()) {
            throw new BadRequestException('You can not delete yourself');
        }

        /** @var \Dvsa\Olcs\Api\Entity\User\User $user */
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

        $userId = $user->getId();
        $this->clearUserCaches([$userId]);

        $result = new Result();
        $result->addId('user', $userId);
        $result->addMessage('User deleted successfully');

        return $result;
    }
}
