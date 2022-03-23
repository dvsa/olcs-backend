<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ResetPasswordOpenAm as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Service\EventHistory\Creator as EventHistoryCreator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Http\Response;
use Olcs\Logging\Log\Logger;

class ResetPasswordOpenAm extends AbstractCommandHandler
{
    private ValidatableAdapterInterface $adapter;
    private EventHistoryCreator $eventHistoryCreator;
    protected $repoServiceName = 'User';

    const MSG_EXPIRED_LINK = 'auth.forgot-password-expired';
    const MSG_GENERIC_FAIL = 'auth.reset-password.fail';
    const MSG_GENERIC_SUCCESS = 'auth.reset-password.success';
    const MSG_FAIL_DEBUG_LOG = '%s failed to reset password: %s';
    const MSG_EXCEPTION_ERROR_LOG = 'Reset password exception for %s: %s';

    public function __construct(ValidatableAdapterInterface $adapter, EventHistoryCreator $eventHistoryCreator)
    {
        $this->adapter = $adapter;
        $this->eventHistoryCreator = $eventHistoryCreator;
    }

    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof Cmd);
        $this->result->setFlag('hasExpired', false);

        $this->adapter->setRealm($command->getRealm());
        $username = $command->getUsername();
        $confirmationId = $command->getConfirmationId();
        $tokenId = $command->getTokenId();

        try {
            $isValid = $this->adapter->confirmPasswordResetValid($username, $confirmationId, $tokenId);

            if ($isValid['status'] !== Response::STATUS_CODE_200) {
                $this->result->setFlag('hasExpired', true);
                $this->updateResult(false, self::MSG_EXPIRED_LINK);
                return $this->result;
            }

            $result = $this->adapter->resetPassword($username, $confirmationId, $tokenId, $command->getPassword());

            if ($result['status'] === Response::STATUS_CODE_200) {
                $userRepo = $this->getRepo('User');
                assert($userRepo instanceof UserRepo);
                $user = $userRepo->fetchEnabledIdentityByLoginId($username);

                //create event history record
                $this->eventHistoryCreator->create($user, EventHistoryTypeEntity::EVENT_CODE_PASSWORD_RESET);

                $this->updateResult(true, self::MSG_GENERIC_SUCCESS);
                return $this->result;
            }

            $failReason = $result['message'] ?? self::MSG_GENERIC_FAIL;
            $logMessage = sprintf(self::MSG_FAIL_DEBUG_LOG, $username, $failReason);
            Logger::debug($logMessage);
        } catch (\Exception $e) {
            $failReason = self::MSG_GENERIC_FAIL;
            $logMessage = sprintf(self::MSG_EXCEPTION_ERROR_LOG, $username, $e->getMessage());
            Logger::err($logMessage);
        }

        $this->updateResult(false, $failReason);
        return $this->result;
    }

    /**
     * @param bool   $success
     * @param string $message
     *
     * @return void
     */
    private function updateResult(bool $success, string $message): void
    {
        $this->result->setFlag('success', $success);
        $this->result->addMessage($message);
    }
}
