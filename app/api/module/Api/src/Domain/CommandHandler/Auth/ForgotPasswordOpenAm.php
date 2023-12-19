<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ForgotPasswordOpenAm as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Http\Response;
use Olcs\Logging\Log\Logger;

class ForgotPasswordOpenAm extends AbstractCommandHandler
{
    private ValidatableAdapterInterface $adapter;
    private TranslatorDelegator $translator;

    public const EMAIL_SUBJECT_KEY = 'auth.forgot-password.email.subject';
    public const EMAIL_MESSAGE_KEY = 'auth.forgot-password.email.message';
    public const MSG_GENERIC_FAIL = 'auth.forgot-password.fail';
    public const MSG_GENERIC_SUCCESS = 'auth.forgot-password.success';
    public const MSG_FAIL_DEBUG_LOG = '%s failed to send forgot password email: %s';
    public const MSG_EXCEPTION_ERROR_LOG = 'Forgot password email exception for %s: %s';

    public function __construct(ValidatableAdapterInterface $adapter, TranslatorDelegator $translator)
    {
        $this->adapter = $adapter;
        $this->translator = $translator;
    }

    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof Cmd);
        $this->adapter->setRealm($command->getRealm());
        $username = $command->getUsername();

        try {
            $result = $this->adapter->forgotPassword(
                $command->getUsername(),
                $this->translator->translate(self::EMAIL_SUBJECT_KEY),
                $this->translator->translate(self::EMAIL_MESSAGE_KEY)
            );

            if ($result['status'] === Response::STATUS_CODE_200) {
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
