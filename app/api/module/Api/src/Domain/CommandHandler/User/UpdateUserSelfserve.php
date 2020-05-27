<?php

/**
 * Update User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Grant;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\EventHistory\Creator as EventHistoryCreator;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Update User Selfserve
 */
final class UpdateUserSelfserve extends AbstractUserCommandHandler implements
    TransactionedInterface,
    OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails'];


    /** @var EventHistoryCreator */
    private $eventHistoryCreator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->eventHistoryCreator = $mainServiceLocator->get('EventHistoryCreator');

        return parent::createService($serviceLocator);
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

        /** @var User $user */
        $user = $this->getRepo()->fetchById($command->getId(), Query::HYDRATE_OBJECT, $command->getVersion());

        $data = $command->getArrayCopy();

        // validate username
        $this->validateUsername($data['loginId'], $user->getLoginId());

        // populate roles based on the user type and permission
        $data['roles'] = User::getRolesByUserType($user->getUserType(), $data['permission']);

        $this->checkEmailUpdate($user, $command);

        $user->update(
            $this->getRepo()->populateRefDataReference($data)
        );

        // Forename and surname cannot be updated from self-serve
        $canUpdatePerson = false;
        if ($user->getContactDetails() instanceof ContactDetails) {
            // update existing contact details
            $user->getContactDetails()->update(
                $this->getRepo('ContactDetails')->populateRefDataReference(
                    $command->getContactDetails()
                ),
                $canUpdatePerson
            );
        } else {
            // create new contact details
            $user->setContactDetails(
                ContactDetails::create(
                    $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_USER),
                    $this->getRepo('ContactDetails')->populateRefDataReference(
                        $command->getContactDetails()
                    )
                )
            );
        }

        $this->getRepo()->save($user);

        $this->getOpenAmUser()->updateUser(
            $user->getPid(),
            $user->getLoginId(),
            $command->getContactDetails()['emailAddress']
        );

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User updated successfully');

        return $result;
    }

    private function checkEmailUpdate(User $user, CommandInterface $command)
    {
        if ($user->getContactDetails()->getEmailAddress() != $command->getContactDetails()['emailAddress']) {
            $this->eventHistoryCreator->create($user, EventHistoryTypeEntity::USER_EMAIL_ADDRESS_UPDATED, 'Old:'.$user->getContactDetails()->getEmailAddress().' New:'.$command->getContactDetails()['emailAddress']);
        }
    }
}
