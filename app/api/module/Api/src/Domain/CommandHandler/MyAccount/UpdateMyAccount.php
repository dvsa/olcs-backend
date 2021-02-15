<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as PhoneContactEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;

/**
 * Update MyAccount
 */
final class UpdateMyAccount extends AbstractUserCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    CacheAwareInterface,
    OpenAmUserAwareInterface
{
    use AuthAwareTrait,
        CacheAwareTrait,
        OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['ContactDetails', 'PhoneContact', 'Person', 'Address'];

    /**
     * Handle command
     *
     * @param CommandInterface $command Command parameters
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $data = $command->getArrayCopy();

        /** @var UserEntity $user */
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

        $contactDetails = $user->getContactDetails();
        $cmdContactDetails = $command->getContactDetails();

        if ($contactDetails instanceof ContactDetails) {
            $savePhoneContactsSeparately = true;

            // update existing contact details separately
            if ($this->isInternalUser()) {
                $savedPerson = $this->savePerson($cmdContactDetails['person'], $contactDetails);
                $contactDetails->setPerson($savedPerson);
            }

            $savedAddress = $this->saveAddress($cmdContactDetails['address'], $contactDetails);
            $contactDetails->setAddress($savedAddress);
            $contactDetails->setEmailAddress($cmdContactDetails['emailAddress']);
        } else {
            $savePhoneContactsSeparately = false;

            // create new contact details
            $user->setContactDetails(
                ContactDetails::create(
                    $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_USER),
                    $this->getRepo('ContactDetails')->populateRefDataReference(
                        $cmdContactDetails
                    )
                )
            );
        }

        $this->getRepo()->save($user);

        if ($savePhoneContactsSeparately) {
            $this->savePhoneContacts($cmdContactDetails['phoneContacts'], $contactDetails);
        }

        $this->getOpenAmUser()->updateUser(
            $user->getPid(),
            $command->getLoginId(),
            $cmdContactDetails['emailAddress']
        );

        $userId = $user->getId();
        $this->clearUserCaches([$userId]);

        $result = new Result();
        $result->addId('user', $userId);
        $result->addMessage('User updated successfully');

        return $result;
    }

    /**
     * Save phone contacts
     *
     * @param array          $phoneContacts  phone contacts
     * @param ContactDetails $contactDetails contact details
     *
     * @return void
     */
    protected function savePhoneContacts($phoneContacts, $contactDetails)
    {
        $existingPhoneContacts = $contactDetails->getPhoneContacts();
        /*
         * we do have only one number of each type for internal user
         * and it's unlikely that we have more than one, that's the reason
         * we can safely delete all of them and then insert new/updated numbers
         * this logic should be changed in case the business decide to
         * introduce a table with phone numbers for internal users your account section
         */
        $repo = $this->getRepo('PhoneContact');
        foreach ($existingPhoneContacts as $existingPhoneContact) {
            $repo->delete($existingPhoneContact);
        }
        foreach ($phoneContacts as $phoneContact) {
            $phoneContactEntity = new PhoneContactEntity(
                $repo->getRefdataReference($phoneContact['phoneContactType'])
            );
            $phoneContactEntity->setPhoneNumber($phoneContact['phoneNumber']);
            $phoneContactEntity->setContactDetails($contactDetails);
            $repo->save($phoneContactEntity);
        }
    }

    /**
     * Save person
     *
     * @param array          $person         person
     * @param ContactDetails $contactDetails contact details
     *
     * @return PersonEntity
     */
    protected function savePerson($person, $contactDetails)
    {
        $personEntity = $contactDetails->getPerson();
        if ($personEntity === null) {
            $personEntity = new PersonEntity();
        }

        // title
        $title = null;
        if (isset($person['title'])) {
            $title = $this->getRepo('Person')->getRefdataReference($person['title']);
        }

        // Birth date
        $birthDate = isset($person['birthDate']) ? $person['birthDate'] : null;

        $personEntity->updatePerson(
            $person['forename'],
            $person['familyName'],
            $title,
            $birthDate
        );
        $this->getRepo('Person')->save($personEntity);

        return $personEntity;
    }

    /**
     * Save address
     *
     * @param array          $address        address
     * @param ContactDetails $contactDetails contact details
     *
     * @return AddressEntity
     */
    protected function saveAddress($address, $contactDetails)
    {
        $addressEntity = $contactDetails->getAddress();
        if ($addressEntity === null) {
            $addressEntity = new AddressEntity();
        }
        $addressEntity->updateAddress(
            $address['addressLine1'],
            $address['addressLine2'],
            $address['addressLine3'],
            $address['addressLine4'],
            $address['town'],
            $address['postcode'],
            $this->getRepo('Address')->getReference(CountryEntity::class, $address['countryCode'])
        );

        $this->getRepo('Address')->save($addressEntity);
        return $addressEntity;
    }
}
