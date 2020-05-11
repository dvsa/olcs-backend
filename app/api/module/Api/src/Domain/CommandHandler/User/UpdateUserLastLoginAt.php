<?php

/**
 * Update User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use \Dvsa\Olcs\Transfer\Command\User\UpdateUserLastLoginAt as UpdateUserLastLoginAtCommand;
use Doctrine\ORM\Query;

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
