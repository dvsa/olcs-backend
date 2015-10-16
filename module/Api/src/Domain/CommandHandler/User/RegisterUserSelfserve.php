<?php

/**
 * Register User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Register User Selfserve
 */
final class RegisterUserSelfserve extends AbstractUserCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails', 'Licence', 'Organisation'];

    public function handleCommand(CommandInterface $command)
    {
        $data = $command->getArrayCopy();

        // validate username
        $this->validateUsername($data['loginId']);

        // link with organisations
        $data['organisations'] = $this->getOrganisations($data);

        if (empty($data['organisations'])) {
            // new user has to be linked to an organisation
            throw new BadRequestException('User must be linked to an organisation');
        }

        // register new user as an operator admin
        $data['roles'] = User::getRolesByUserType(User::USER_TYPE_OPERATOR, User::PERMISSION_ADMIN);

        $user = User::create(
            User::USER_TYPE_OPERATOR,
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

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User created successfully');

        return $result;
    }

    private function getOrganisations($data)
    {
        $organisations = [];

        if (!empty($data['licenceNumber'])) {
            // fetch licence by licence number
            $licence = $this->getRepo('Licence')->fetchForUserRegistration($data['licenceNumber']);

            // link with the organisation
            $organisations[] = $licence->getOrganisation();
        } elseif (!empty($data['organisationName'])) {
            // create new organisation
            $organisation = new OrganisationEntity();
            $organisation->setType(
                $this->getRepo()->getRefdataReference($data['businessType'])
            );
            $organisation->setName($data['organisationName']);

            // save
            $this->getRepo('Organisation')->save($organisation);

            // link with the organisation
            $organisations[] = $organisation;
        }

        return $organisations;
    }
}
