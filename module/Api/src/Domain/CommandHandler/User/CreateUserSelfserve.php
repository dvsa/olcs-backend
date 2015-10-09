<?php

/**
 * Create User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create User Selfserve
 */
final class CreateUserSelfserve extends AbstractCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    OpenAmUserAwareInterface
{
    use AuthAwareTrait,
        OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_SELFSERVE)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $data = $command->getArrayCopy();

        // validate username
        $this->validateUsername($data['loginId']);

        // copy user type from the current loggedin user
        switch ($this->getCurrentUser()->getUserType()) {
            case User::USER_TYPE_PARTNER:
                $data['userType'] = User::USER_TYPE_PARTNER;
                $data['partnerContactDetails'] = $this->getCurrentUser()->getPartnerContactDetails()->getId();
                break;
            case User::USER_TYPE_LOCAL_AUTHORITY:
                $data['userType'] = User::USER_TYPE_LOCAL_AUTHORITY;
                $data['localAuthority'] = $this->getCurrentUser()->getLocalAuthority()->getId();
                break;
            case User::USER_TYPE_OPERATOR:
                $data['userType'] = User::USER_TYPE_OPERATOR;
                $data['organisations'] = array_map(
                    function ($item) {
                        return $item->getOrganisation();
                    },
                    $this->getCurrentUser()->getOrganisationUsers()->toArray()
                );
                break;
            default:
                // only available to specific user types
                throw new BadRequestException('User type must be provided');
        }

        // populate roles based on the user type and permission
        $data['roles'] = User::getRolesByUserType($data['userType'], $data['permission']);

        $user = User::create(
            $this->getOpenAmUser()->reservePid(),
            $data['userType'],
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

        $this->getOpenAmUser()->registerUser(
            $command->getLoginId(),
            $command->getContactDetails()['emailAddress'],
            $command->getContactDetails()['person']['forename'],
            $command->getContactDetails()['person']['familyName'],
            Client::REALM_SELFSERVE
        );

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User created successfully');

        return $result;
    }
}
