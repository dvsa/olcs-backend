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
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationUser as OrganisationUserRepository;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepository;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\User\DeleteUser as DeleteUserCommand;

/**
 * Delete User
 */
final class DeleteUser extends AbstractCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    CacheAwareInterface,
    OpenAmUserAwareInterface
{
    use AuthAwareTrait,
        CacheAwareTrait,
        OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Task', 'OrganisationUser'];

    /**
     * @param CommandInterface|DeleteUserCommand $command command
     *
     * @return Result result
     * @throws BadRequestException
     * @throws FailedRequestException
     * @throws ForbiddenException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->guardAgainstLackOfPermission();

        $user = $this->getUser($command);

        $this->guardAgainstOpenTasks($user);

        $this->deleteUser($user);

        $this->clearUserCaches([$user->getId()]);

        return $this->createResult($user);
    }

    /**
     * @return void
     * @throws ForbiddenException
     */
    private function guardAgainstLackOfPermission()
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }
    }

    /**
     * @param User $user user
     *
     * @return void
     * @throws BadRequestException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function guardAgainstOpenTasks(User $user)
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->getRepo('Task');

        if (!empty($taskRepository->fetchByUser($user->getId(), true))) {
            // the user still has some open tasks
            throw new BadRequestException('ERR_USER_HAS_OPEN_TASK');
        }
    }

    /**
     * @param User $user user
     *
     * @return void
     * @throws FailedRequestException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    private function deleteUser(User $user)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepo();
        $userRepository->delete($user);

        /** @var OrganisationUserRepository $organisationUserRepository */
        $organisationUserRepository = $this->getRepo('OrganisationUser');
        $organisationUserRepository->deleteByUserId($user->getId());

        try {
            $this->getOpenAmUser()->disableUser($user->getPid());
        } catch (FailedRequestException $exception) {
            // if the OpenAM user is already gone (i.e. 404) then we don't need to delete. Otherwise this is an error
            if ($exception->getResponse()->getStatusCode() !== 404) {
                throw $exception;
            }
        }
    }

    /**
     * @param CommandInterface $command command
     *
     * @return User
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function getUser(CommandInterface $command)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepo();
        return $userRepository->fetchUsingId($command);
    }

    /**
     * @param User $user user
     *
     * @return Result
     */
    protected function createResult(User $user)
    {
        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User deleted successfully');
        return $result;
    }
}
