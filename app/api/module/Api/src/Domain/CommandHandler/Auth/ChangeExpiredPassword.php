<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepository;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Transfer\Command\Auth\ChangeExpiredPassword as ChangeExpiredPasswordCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Authentication\Result as AuthResult;

/**
 * @see ChangeExpiredPasswordFactory
 */
class ChangeExpiredPassword extends AbstractCommandHandler
{
    public const ERROR_USER_NOT_FOUND = 'Updating lastLoginAt failed: loginId is not found in User table';

    /**
     * @var ValidatableAdapterInterface|OpenAm|CognitoAdapter
     */
    protected ValidatableAdapterInterface $adapter;
    private UserRepository $userRepository;

    /**
     * @param ValidatableAdapterInterface $adapter
     */
    public function __construct(ValidatableAdapterInterface $adapter, \Dvsa\Olcs\Api\Domain\Repository\User $userRepository)
    {
        $this->adapter = $adapter;
        $this->userRepository = $userRepository;
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof ChangeExpiredPasswordCommand);

        $changeResult = $this->adapter->changeExpiredPassword(
            $command->getNewPassword(),
            $command->getChallengeSession(),
            $command->getUsername(),
        );

        $this->updateUserLastLoginAt($changeResult, $command->getUsername());

        $this->result->setFlag('isValid', $changeResult->isValid());
        $this->result->setFlag('code', $changeResult->getCode());
        $this->result->setFlag('identity', $changeResult->getIdentity());
        $this->result->setFlag('messages', $changeResult->getMessages());
        $this->result->setFlag('options', $changeResult->getOptions());

        return $this->result;
    }

    /**
     * @throws RuntimeException
     */
    protected function updateUserLastLoginAt(ChangeExpiredPasswordResult $result, string $loginId): void
    {
        if ($result->getCode() !== ChangeExpiredPasswordResult::SUCCESS) {
            return;
        }

        $user = $this->userRepository->fetchByLoginId($loginId)[0] ?? null;
        if (is_null($user)) {
            throw new RuntimeException(static::ERROR_USER_NOT_FOUND);
        }
        assert($user instanceof User);
        $user->setLastLoginAt(new \DateTime());
        $this->userRepository->save($user);
    }
}
