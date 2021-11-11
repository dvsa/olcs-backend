<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Transfer\Command\Auth\RefreshToken as RefreshTokenCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Laminas\Http\Response;
use Olcs\Logging\Log\Logger;

class RefreshToken extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

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
        assert($command instanceof RefreshTokenCommand);

        $result = $this->adapter->refreshToken($command->getRefreshToken(), $this->getCurrentUser()->getLoginId());

        $this->result->setFlag('isValid', $result->isValid());
        $this->result->setFlag('code', $result->getCode());
        $this->result->setFlag('identity', $result->getIdentity());
        $this->result->setFlag('messages', $result->getMessages());

        return $this->result;
    }
}
