<?php

/**
 * Create User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

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
use Dvsa\Olcs\Transfer\Command\CommandInterface;

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
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }
        /** @var \Dvsa\Olcs\Transfer\Command\User\CreateUser $command */

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
            $this->getOpenAmUser()->generatePid($command->getLoginId()),
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

        $password = null;

        $realm = Client::REALM_SELFSERVE;

        if ($user->getUserType() === User::USER_TYPE_INTERNAL) {
            $realm = Client::REALM_INTERNAL;
        }

        $this->getOpenAmUser()->registerUser(
            $command->getLoginId(),
            $command->getContactDetails()['emailAddress'],
            $realm,
            function ($params) use (&$password) {
                $password = $params['password'];
            }
        );

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
}
