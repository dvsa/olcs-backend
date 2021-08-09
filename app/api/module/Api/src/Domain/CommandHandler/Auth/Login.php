<?php
declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepository;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Auth\Service\AuthenticationServiceInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Authentication\AuthenticationService;

class Login extends AbstractCommandHandler
{
    /** @var AuthenticationServiceInterface */
    protected $authenticationService;

    /**
     * @var ValidatableAdapterInterface
     */
    protected $adapter;

    protected $repoServiceName = 'User';

    /**
     * Login constructor.
     * @param AuthenticationService $authenticationService
     * @param ValidatableAdapterInterface $adapter
     */
    public function __construct(AuthenticationServiceInterface $authenticationService, ValidatableAdapterInterface $adapter)
    {
        $this->authenticationService = $authenticationService;
        $this->adapter = $adapter;
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof \Dvsa\Olcs\Transfer\Command\Auth\Login);

        $this->adapter->setIdentity($command->getUsername());
        $this->adapter->setCredential($command->getPassword());

        $result = $this->authenticationService->authenticate($this->adapter);
        $this->updateUserLastLoginAt($result, $command->getUsername());

        $this->result->setFlag('isValid', $result->isValid());
        $this->result->setFlag('code', $result->getCode());
        $this->result->setFlag('identity', $result->getIdentity());
        $this->result->setFlag('messages', $result->getMessages());

        return $this->result;
    }

    /**
     * @throws RuntimeException
     */
    protected function updateUserLastLoginAt(\Laminas\Authentication\Result $result, string $loginId)
    {
        if ($result->getCode() !== \Laminas\Authentication\Result::SUCCESS) {
            return;
        }

        $repo = $this->getRepo();
        assert($repo instanceof UserRepository);

        $user = $repo->fetchByLoginId($loginId);
        if (empty($user)) {
            throw new RuntimeException(
                'Updating lastLoginAt failed: loginId is not found in User table'
            );
        }
        $user = $user[0];

        assert($user instanceof User);
        $user->setLastLoginAt(new \DateTime());
        $this->getRepo()->save($user);
    }
}
