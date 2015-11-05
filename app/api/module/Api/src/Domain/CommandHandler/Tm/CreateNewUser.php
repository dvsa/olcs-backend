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
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\User\Role;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Tm\CreateNewUser as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Create New User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateNewUser extends AbstractUserCommandHandler implements TransactionedInterface
{
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
     * @param Cmd $command
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

        $this->result->addId('transportManagerApplicationId', $transportManagerApplication->getId());

        if ($command->getHasEmail() === 'Y') {
            $user = $this->createUser($username, $transportManagerApplication, $contactDetails);
            $this->result->addId('userId', $user->getId());
            $this->result->addMessage('New user created');
        }

        $this->result->addMessage('New transport manager created');

        return $this->result;
    }

    /**
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
     * @param Cmd $command
     * @param ContactDetails $contactDetails
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
     * @param TransportManager $transportManager
     * @param Application $application
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
     * @param $username
     * @param TransportManagerApplication $transportManagerApplication
     * @param ContactDetails $contactDetails
     * @return User
     */
    protected function createUser(
        $username,
        TransportManagerApplication $transportManagerApplication,
        ContactDetails $contactDetails
    ) {
        $userData = [
            'roles' => [
                $this->getRepo('Role')->fetchOneByRole(Role::ROLE_OPERATOR_TM)
            ],
            'loginId' => $username,
            'transportManager' => $transportManagerApplication->getTransportManager()
        ];

        $user = User::create(User::USER_TYPE_TRANSPORT_MANAGER, $userData);
        $user->setContactDetails($contactDetails);

        $this->getRepo('User')->save($user);

        // TODO - replace with the generated password
        $password = 'GENERATED_PASSWORD_HERE';

        // send welcome email
        $this->handleSideEffect(
            SendTmUserCreatedDto::create(
                [
                    'user' => $user,
                    'tma' => $transportManagerApplication
                ]
            )
        );

        // send temporary password email
        $this->handleSideEffect(
            SendUserTemporaryPasswordDto::create(
                [
                    'user' => $user,
                    'password' => $password,
                ]
            )
        );

        return $user;
    }

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
