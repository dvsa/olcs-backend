<?php

declare(strict_types=1);

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

/**
 * @see ChangeExpiredPasswordFactory
 */
class ChangeExpiredPassword extends AbstractCommandHandler
{
    public const ERROR_USER_NOT_FOUND = 'Updating lastLoginAt failed: loginId is not found in User table';

    //seems sensible to use the same keys as the regular change password
    const MSG_GENERIC_FAIL = 'auth.change-password.fail';
    const MSG_GENERIC_SUCCESS = 'auth.change-password.success';
    const MSG_NOT_AUTHORIZED = 'auth.change-password.not-authorized';
    const MSG_INVALID = 'auth.change-password.invalid';

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

        $isValid = $changeResult->isValid();
        $this->result->setFlag('isValid', $isValid);
        $this->result->setFlag('code', $changeResult->getCode());
        $this->result->setFlag('identity', $changeResult->getIdentity());
        $this->result->setFlag('options', $changeResult->getOptions());

        $message = self::MSG_GENERIC_SUCCESS;

        if (!$isValid) {
            $code = $changeResult->getCode();

            switch ($code) {
                case ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID:
                    $message = self::MSG_INVALID;
                    break;
                case ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED:
                    $message = self::MSG_NOT_AUTHORIZED;
                    break;
                default:
                    $message = self::MSG_GENERIC_FAIL;
            }
        }

        $this->result->setFlag('messages', [$message]);
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
