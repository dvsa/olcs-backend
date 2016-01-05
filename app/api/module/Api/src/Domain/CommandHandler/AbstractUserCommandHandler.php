<?php

/**
 * User Abstract
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Exception\RollbackUserCreatedException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * User Abstract
 */
abstract class AbstractUserCommandHandler extends AbstractCommandHandler
{
    const ERR_USERNAME_EXISTS = 'ERR_USERNAME_EXISTS';

    protected $repoServiceName = 'User';

    protected $usernameErrorKey = 'loginId';

    /**
     * Validates username
     *
     * @param string $new
     * @param string $current
     *
     * @return void
     * @throws ValidationException
     */
    protected function validateUsername($new, $current = null)
    {
        if (isset($current) && ($new === $current)) {
            // username has not changed
            return true;
        }

        $users = $this->getRepo('User')->fetchByLoginId($new);

        if (!empty($users)) {
            throw new ValidationException([$this->usernameErrorKey => [self::ERR_USERNAME_EXISTS]]);
        }
    }

    /**
     * Rollback the command
     *
     * @param CommandInterface $command
     * @param \Exception $exception
     *
     * @return void
     * @throws \Exception
     */
    public function rollbackCommand(CommandInterface $command, \Exception $exception)
    {
        if ($exception instanceof RollbackUserCreatedException) {
            $loginId = method_exists($command, 'getUsername') ? $command->getUsername() : $command->getLoginId();

            try {
                // delete user in OpenAM
                $this->getOpenAmUser()->deleteUser(
                    $loginId
                );
            } catch (\Exception $e) {
                // unable to rollback - log some details
                $this->logRollbackCommandFailure(
                    new \Exception(
                        'Unable to rollback command for loginId "' . $loginId . '" due to "' . $e->getMessage() . '"',
                        null,
                        $exception
                    )
                );
            }
        }
    }
}
