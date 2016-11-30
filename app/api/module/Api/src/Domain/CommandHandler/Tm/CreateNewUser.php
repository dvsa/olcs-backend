<?php

/**
 * Create New User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\Command\Email\SendTmUserCreated as SendTmUserCreatedDto;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\User\Role;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Tm\CreateNewUser as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;

/**
 * Create New User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateNewUser extends AbstractUserCommandHandler implements TransactionedInterface, OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    const ERR_EMAIL_REQUIRED = 'ERR_EMAIL_REQUIRED';
    const ERR_USERNAME_REQUIRED = 'ERR_USERNAME_REQUIRED';

    protected $extraRepos = [
        'Application',
        'ContactDetails',
        'Person',
        'TransportManager',
        'TransportManagerApplication',
        'Address',
        'Role'
    ];

    protected $usernameErrorKey = 'username';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $username = trim($command->getUsername());
        $emailAddress = trim($command->getEmailAddress());

        if ($command->getHasEmail() === 'Y') {
            $this->validateRequired($username, $emailAddress);
            $this->validateUsername($username);
        }

        $application = $this->getRepo('Application')->fetchById($command->getApplication());

        $person = $this->createPerson($command);
        $this->result->addId('personId', $person->getId());

        $contactDetails = $this->createContactDetails($emailAddress, $person);
        $this->result->addId('contactDetailsId', $contactDetails->getId());

        $transportManager = $this->createTransportManager($contactDetails);
        $this->result->addId('transportManagerId', $transportManager->getId());

        $transportManagerApplication = $this->createTmApplication($transportManager, $application, $command);

        $this->result->merge(
            $this->handleSideEffect(
                UpdateApplicationCompletion::create(
                    ['id' => $command->getApplication(), 'section' => 'transportManagers']
                )
            )
        );

        $this->result->addId('transportManagerApplicationId', $transportManagerApplication->getId());

        if ($command->getHasEmail() === 'Y') {
            $user = $this->createUser($command, $transportManagerApplication, $contactDetails);
            $this->result->addId('userId', $user->getId());
            $this->result->addMessage('New user created');
        }

        $this->result->addMessage('New transport manager created');

        return $this->result;
    }

    /**
     * Create contact details
     *
     * @param string $emailAddress Email address
     * @param Person $person       Person
     *
     * @return ContactDetails
     */
    protected function createContactDetails($emailAddress, Person $person)
    {
        $address = new Address();
        $this->getRepo('Address')->save($address);

        $contactDetails = new ContactDetails(
            $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER)
        );
        $contactDetails->setAddress($address);

        if (!empty($emailAddress)) {
            $contactDetails->setEmailAddress($emailAddress);
        }

        $contactDetails->setPerson($person);

        $this->getRepo('ContactDetails')->save($contactDetails);

        return $contactDetails;
    }

    /**
     * Create person
     *
     * @param Cmd $command Command
     *
     * @return Person
     */
    protected function createPerson(Cmd $command)
    {
        $person = new Person();
        $person->setForename($command->getFirstName());
        $person->setFamilyName($command->getFamilyName());
        $person->setBirthDate(new DateTime($command->getBirthDate()));

        $this->getRepo('Person')->save($person);

        return $person;
    }

    /**
     * Create Transport Manager
     *
     * @param ContactDetails $contactDetails Contact details
     *
     * @return TransportManager
     */
    protected function createTransportManager(ContactDetails $contactDetails)
    {
        $address = new Address();
        $this->getRepo('Address')->save($address);

        $workCd = new ContactDetails(
            $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER)
        );
        $workCd->setAddress($address);
        $this->getRepo('ContactDetails')->save($workCd);

        $transportManager = new TransportManager();
        $transportManager->setHomeCd($contactDetails);
        $transportManager->setWorkCd($workCd);

        $transportManager->setTmStatus(
            $this->getRepo()->getRefdataReference(TransportManager::TRANSPORT_MANAGER_STATUS_CURRENT)
        );

        $this->getRepo('TransportManager')->save($transportManager);

        return $transportManager;
    }

    /**
     * Create TM Application
     *
     * @param TransportManager $transportManager Transport Manager
     * @param Application      $application      Application
     * @param CommandInterface $command          Command
     *
     * @return TransportManagerApplication
     */
    protected function createTmApplication(TransportManager $transportManager, Application $application, Cmd $command)
    {
        $transportManagerApplication = new TransportManagerApplication();
        $transportManagerApplication->setTransportManager($transportManager);
        $transportManagerApplication->setApplication($application);
        $transportManagerApplication->setAction('A');

        if ($command->getHasEmail() === 'Y') {
            $status = $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_INCOMPLETE);
        } else {
            $status = $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_POSTAL_APPLICATION);
        }

        $transportManagerApplication->setTmApplicationStatus($status);

        $this->getRepo('TransportManagerApplication')->save($transportManagerApplication);

        return $transportManagerApplication;
    }

    /**
     * Create user
     *
     * @param \Dvsa\Olcs\Transfer\Command\Tm\CreateNewUser $command                     Command
     * @param TransportManagerApplication                  $transportManagerApplication TM Application
     * @param ContactDetails                               $contactDetails              Contact Details
     *
     * @return User
     */
    protected function createUser(
        $command,
        TransportManagerApplication $transportManagerApplication,
        ContactDetails $contactDetails
    ) {
        $userData = [
            'roles' => [
                $this->getRepo('Role')->fetchOneByRole(Role::ROLE_OPERATOR_TM)
            ],
            'loginId' => $command->getUsername(),
            'translateToWelsh' => $command->getTranslateToWelsh(),
            'transportManager' => $transportManagerApplication->getTransportManager()
        ];

        $pid = $this->getOpenAmUser()->generatePid($command->getUsername());

        $user = User::create($pid, User::USER_TYPE_TRANSPORT_MANAGER, $userData);
        $user->setContactDetails($contactDetails);

        $organisationUser = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser();
        $organisationUser->setUser($user);
        $organisationUser->setOrganisation(
            $transportManagerApplication->getApplication()->getLicence()->getOrganisation()
        );
        $user->addOrganisationUsers($organisationUser);

        $this->getRepo('User')->save($user);

        $password = null;

        $this->getOpenAmUser()->registerUser(
            $command->getUsername(),
            $command->getEmailAddress(),
            Client::REALM_SELFSERVE,
            function ($params) use (&$password) {
                $password = $params['password'];
            }
        );

        try {
            // send welcome email
            $this->handleSideEffect(
                SendTmUserCreatedDto::create(
                    [
                        'user' => $user->getId(),
                        'tma' => $transportManagerApplication->getId()
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

        return $user;
    }

    /**
     * Validate required
     *
     * @param string $username     Username
     * @param string $emailAddress Email address
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function validateRequired($username, $emailAddress)
    {
        $messages = [];

        if (empty($username)) {
            $messages['username'] = [self::ERR_USERNAME_REQUIRED];
        }

        if (empty($emailAddress)) {
            $messages['emailAddress'] = [self::ERR_EMAIL_REQUIRED];
        }

        if (!empty($messages)) {
            throw new ValidationException($messages);
        }
    }
}
