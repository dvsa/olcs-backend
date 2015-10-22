<?php

/**
 * Update MyAccount
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update MyAccount
 */
final class UpdateMyAccount extends AbstractUserCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        $data = $command->getArrayCopy();

        $user = $this->getRepo()->fetchById(
            $this->getCurrentUser()->getId(),
            Query::HYDRATE_OBJECT,
            $command->getVersion()
        );

        // validate username
        $this->validateUsername($data['loginId'], $user->getLoginId());

        $user->update(
            $this->getRepo()->populateRefDataReference($data)
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
        $result->addMessage('User updated successfully');

        return $result;
    }
}
