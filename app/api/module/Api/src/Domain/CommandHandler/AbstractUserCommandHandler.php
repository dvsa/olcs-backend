<?php

/**
 * User Abstract
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

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
}
