<?php

/**
 * Create User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create User
 */
final class CreateUser extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        $user = User::create(
            $command->getUserType(),
            $this->getRepo()->populateRefDataReference(
                $command->getArrayCopy()
            )
        );

        if ($user->getContactDetails() instanceof ContactDetails) {
            // update existing contact details
            $user->getContactDetails()->update(
                $this->getRepo('ContactDetails')->populateRefDataReference(
                    $command->getContactDetails()
                )
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

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User created successfully');

        return $result;
    }
}
