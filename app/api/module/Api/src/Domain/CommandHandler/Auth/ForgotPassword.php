<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ForgotPasswordOpenAm;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Domain\Repository\UserPasswordReset as UserPasswordResetRepo;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\User\UserPasswordReset as UserPasswordResetEntity;
use Dvsa\Olcs\Transfer\Command\Auth\ForgotPassword as ForgotPasswordCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendForgotPassword as ForgotPasswordEmailCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class ForgotPassword extends AbstractCommandHandler implements ConfigAwareInterface
{
    use ConfigAwareTrait;
    use QueueAwareTrait;

    protected $repoServiceName = 'UserPasswordReset';
    protected $extraRepos = ['User'];

    const OPENAM_ADAPTER_CONFIG_VALUE = 'openam';
    const MSG_USER_NOT_FOUND = 'User not found'; //preserves existing behaviour, translation key + new message needed
    const MSG_USER_NOT_ALLOWED_RESET = 'account-not-active'; //preserves existing behaviour, review needed

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof ForgotPasswordCmd);

        $userRepo = $this->getRepo('User');
        assert($userRepo instanceof UserRepo);

        $user = $userRepo->fetchEnabledIdentityByLoginId($command->getUsername());

        if ($user === null) {
            $this->result->setFlag('success', false);
            $this->result->addMessage(self::MSG_USER_NOT_FOUND);
            return $this->result;
        }

        assert($user instanceof UserEntity);

        if (!$user->canResetPassword()) {
            $this->result->setFlag('success', false);
            $this->result->addMessage(self::MSG_USER_NOT_ALLOWED_RESET);
            return $this->result;
        }

        /** @todo can be removed once OpenAm is gone, as can interface & trait for accessing config */
        if ($this->config['auth']['default_adapter'] === self::OPENAM_ADAPTER_CONFIG_VALUE) {
            return $this->proxyCommand($command, ForgotPasswordOpenAm::class);
        }

        //@todo check for existing reset? Do we want to do anything to prevent spamming of this service?
        $confirmation = hash('sha256', random_bytes(512));
        $entity = UserPasswordResetEntity::create($user, $confirmation);

        $userPasswordResetRepo = $this->getRepo('UserPasswordReset');
        assert($userPasswordResetRepo instanceof UserPasswordResetRepo);
        $userPasswordResetRepo->save($entity);

        $id = $entity->getId();

        $emailCmdData = [
            'id' => $id,
            'realm' => $command->getRealm(),
        ];

        $emailQueueCmd = $this->emailQueue(ForgotPasswordEmailCmd::class, $emailCmdData, $id);
        $this->result->merge(
            $this->handleSideEffect($emailQueueCmd)
        );

        $this->result->addId('UserPasswordReset', $id);
        $this->result->setFlag('success', true);

        return $this->result;
    }
}
