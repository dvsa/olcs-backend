<?php

/**
 * Update User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update User
 */
final class UpdateUser extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Application', 'ContactDetails', 'Licence'];

    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::INTERNAL_ADMIN)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $data = $command->getArrayCopy();

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

        $user = $this->getRepo()->fetchById($command->getId(), Query::HYDRATE_OBJECT, $command->getVersion());

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
