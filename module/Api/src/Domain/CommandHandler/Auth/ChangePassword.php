<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Laminas\Http\Response;
use Olcs\Logging\Log\Logger;

class ChangePassword extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    const MSG_EXCEPTION_ERROR_LOG = 'Change password exception for %s: %s';
    const MSG_FAIL_DEBUG_LOG = '%s failed to change password: %s';
    const MSG_ANON_USER = 'Anonymous users can\'t change passwords';
    const MSG_GENERIC_FAIL = 'auth.change-password.fail';
    const MSG_GENERIC_SUCCESS = 'auth.change-password.success';

    /**
     * @var ValidatableAdapterInterface|OpenAm|CognitoAdapter
     */
    protected ValidatableAdapterInterface $adapter;

    /**
     * @param ValidatableAdapterInterface $adapter
     */
    public function __construct(ValidatableAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof \Dvsa\Olcs\Transfer\Command\Auth\ChangePassword);

        //get the user who is currently logged in
        $username = $this->getCurrentUser()->getLoginId();

        //set the realm on the adapter e.g. internal/selfserve
        $this->adapter->setRealm($command->getRealm());

        try {
            $changeResult = $this->adapter->changePassword(
                $username,
                $command->getPassword(),
                $command->getNewPassword()
            );

            if ($changeResult['status'] === Response::STATUS_CODE_200) {
                $this->updateResult(true, self::MSG_GENERIC_SUCCESS);
                return $this->result;
            }

            $failReason = $changeResult['reason'] ?? self::MSG_GENERIC_FAIL;
            $logMessage = sprintf(self::MSG_FAIL_DEBUG_LOG, $username, $failReason);
            Logger::debug($logMessage);
        } catch(ChangePasswordException $e) {
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
