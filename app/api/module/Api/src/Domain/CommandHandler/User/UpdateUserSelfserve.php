<?php

/**
 * Update User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

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

        $user->update(
            $this->getRepo()->populateRefDataReference($data)
        );

        // If the user is a transport manager, then no Self Serve updates to Forename or Surname are allowed.
        $canUpdatePerson = $user->getUserType() == User::USER_TYPE_TRANSPORT_MANAGER ? false : true;

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
}
