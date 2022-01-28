<?php

/**
 * Create User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserCreated as SendUserCreatedDto;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\User\CreateUser as CreateUserCommand;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;

/**
 * Create User
 */
final class CreateUser extends AbstractUserCommandHandler implements
    TransactionedInterface,
    OpenAmUserAwareInterface,
    AuthAwareInterface
{
    use OpenAmUserAwareTrait;
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Application', 'ContactDetails', 'Licence'];

    /**
     * @var ValidatableAdapterInterface | \Dvsa\Olcs\Api\Service\OpenAm\UserInterface
     */
    private $adapter;

    /**
     * @var PasswordService
     */
    private $passwordService;

    /**
     * CreateUser constructor.
     *
     * @param PasswordService $passwordService
     * @param ValidatableAdapterInterface|null $adapter
     */
    public function __construct(PasswordService $passwordService, ?ValidatableAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->passwordService = $passwordService;
    }


    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        assert($command instanceof CreateUserCommand);

        //TODO: Remove once OpenAM is removed.
        if (is_null($this->adapter)) {
            $this->adapter = $this->getOpenAmUser();
        }

        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }
        /** @var CreateUserCommand $command */

        $data = $command->getArrayCopy();

        // validate username
        $this->validateUsername($data['loginId']);

        // validate roles
        $this->validateRoles($data['roles']);

        if (($command->getUserType() === User::USER_TYPE_OPERATOR) && (!empty($data['licenceNumber']))) {
            // fetch licence by licence number
            $licence = $this->getRepo('Licence')->fetchByLicNo($data['licenceNumber']);

            // link with the organisation
            $data['organisations'] = [$licence->getOrganisation()];
        } elseif (($command->getUserType() === User::USER_TYPE_TRANSPORT_MANAGER) && (!empty($data['application']))) {
            // fetch application by id
            $application = $this->getRepo('Application')->fetchWithLicenceAndOrg($data['application']);

            // link with the organisation
            $data['organisations'] = [$application->getLicence()->getOrganisation()];
        }

        if (in_array($command->getUserType(), [User::USER_TYPE_OPERATOR, User::USER_TYPE_TRANSPORT_MANAGER])
            && (empty($data['organisations']))
        ) {
            throw new ValidationException(['New user must belong to an organisation']);
        }

        $user = User::create(
            $this->generatePid($command->getLoginId()),
            $command->getUserType(),
            $this->getRepo()->populateRefDataReference($data)
        );

        // create new contact details
        $user->setContactDetails(
            ContactDetails::create(
                $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_USER),
                $this->getRepo('ContactDetails')->populateRefDataReference(
                    $command->getContactDetails()
                )
            )
        );

        $this->getRepo()->save($user);

        $password = $this->passwordService->generatePassword();
        $realm = Client::REALM_SELFSERVE;

        if ($user->getUserType() === User::USER_TYPE_INTERNAL) {
            $realm = Client::REALM_INTERNAL;
        }

        try {
            $this->storeUserInAuthService($command, $password, $realm);
        } catch (ClientException | FailedRequestException $e) {
            $this->getRepo()->delete($user);
            throw new \Exception("Unable to store user in Auth Service", $e->getCode(), $e);
        }

        try {
            // send welcome email
            $this->handleSideEffect(
                SendUserCreatedDto::create(
                    [
                        'user' => $user->getId(),
                    ]
                )
            );

            // send temporary password email
            $this->handleSideEffect(
                SendUserTemporaryPasswordDto::create(
                    [
                        'user' => $user->getId(),
                        'password' => $password,
                    ]
                )
            );
        } catch (\Exception $e) {
            // swallow any exception
        }

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User created successfully');

        return $result;
    }

    /**
     * @param string $loginId
     * @return string
     * @todo: Remove once OpenAM removed
     */
    private function generatePid(string $loginId)
    {
        if ($this->adapter instanceof ValidatableAdapterInterface) {
            return null;
        }
        return $this->adapter->generatePid($loginId);
    }

    /**
     * @throws FailedRequestException
     * @throws ClientException
     * @todo: Call directly from handle() once OpenAM removed
     */
    private function storeUserInAuthService(CreateUserCommand $command, string &$password, string $realm)
    {
        if ($this->adapter instanceof ValidatableAdapterInterface) {
            $this->adapter->register(
                $command->getLoginId(),
                $password,
                $command->getContactDetails()['emailAddress']
            );
        } else {
            $this->adapter->registerUser(
                $command->getLoginId(),
                $command->getContactDetails()['emailAddress'],
                $realm,
                function ($params) use (&$password) {
                    $password = $params['password'];
                }
            );
        }
    }
}
