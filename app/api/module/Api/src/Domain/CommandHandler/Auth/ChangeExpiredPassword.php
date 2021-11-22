<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;

class ChangeExpiredPassword extends AbstractCommandHandler
{
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
        assert($command instanceof \Dvsa\Olcs\Transfer\Command\Auth\ChangeExpiredPassword);

        $changeResult = $this->adapter->changeExpiredPassword(
            $command->getNewPassword(),
            $command->getChallengeSession(),
            $command->getUsername(),
        );

        $this->result->setFlag('isValid', $changeResult->isValid());
        $this->result->setFlag('code', $changeResult->getCode());
        $this->result->setFlag('identity', $changeResult->getIdentity());
        $this->result->setFlag('messages', $changeResult->getMessages());
        $this->result->setFlag('options', $changeResult->getOptions());

        return $this->result;
    }
}
