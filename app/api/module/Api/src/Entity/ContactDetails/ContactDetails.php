<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * ContactDetails Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="contact_details",
 *    indexes={
 *        @ORM\Index(name="ix_contact_details_person_id", columns={"person_id"}),
 *        @ORM\Index(name="ix_contact_details_address_id", columns={"address_id"}),
 *        @ORM\Index(name="ix_contact_details_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_contact_details_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_contact_details_contact_type", columns={"contact_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_contact_details_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class ContactDetails extends AbstractContactDetails
{
    const TRANSPORT_MANAGER_STATUS_CURRENT = 'tm_s_cur';
    const TRANSPORT_MANAGER_STATUS_ACTIVE = 'tm_st_act';
    const TRANSPORT_MANAGER_STATUS_DISABLED = 'tm_st_disa';
    const TRANSPORT_MANAGER_TYPE_EXTERNAL = 'tm_t_e';
    const TRANSPORT_MANAGER_TYPE_BOTH = 'tm_t_b';

    const CONTACT_TYPE_REGISTERED_ADDRESS = 'ct_reg';
    const CONTACT_TYPE_COMPLAINANT = 'ct_complainant';
    const CONTACT_TYPE_ESTABLISHMENT_ADDRESS = 'ct_est';
    const CONTACT_TYPE_CORRESPONDENCE_ADDRESS = 'ct_corr';
    const CONTACT_TYPE_TRANSPORT_CONSULTANT = 'ct_tcon';
    const CONTACT_TYPE_TRANSPORT_MANAGER = 'ct_tm';
    const CONTACT_TYPE_WORKSHOP = 'ct_work';
    const CONTACT_TYPE_IRFO_OPERATOR = 'ct_irfo_op';
    const CONTACT_TYPE_PARTNER = 'ct_partner';
    const CONTACT_TYPE_OBJECTOR = 'ct_obj';

    public function __construct(RefData $contactType, $contactParams = [])
    {
        parent::__construct();
        $this->setContactType($contactType);
        switch($contactType) {
            case self::CONTACT_TYPE_OBJECTOR:
                return ContactDetails::objector(
                    $contactParams['address'],
                    $contactParams['person'],
                    $contactParams['phoneContacts'],
                    $contactParams['emailAddress']
                );
        }
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    public function update(array $contactParams)
    {
        // each type may have different update
        switch($this->getContactType()->getId()) {
            case self::CONTACT_TYPE_IRFO_OPERATOR:
                $this->updateIrfoOperator($contactParams);
                break;
            case self::CONTACT_TYPE_PARTNER:
                $this->updatePartner($contactParams);
                break;
        }

        return $this;
    }


    /**
     * Create person object
     * @param Cmd $command
     * @return Person|null
     */
    private function createPersonObject(Cmd $command)
    {
        if (!empty($command->getForename()) || !empty($command->getForename())) {
            $person = new Person();
            $person->setForename($command->getForename());
            $person->setFamilyName($command->getFamilyName());
            return $person;
        }
        return null;
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\User\UpdatePartner
     */
    private function updatePartner(array $contactParams)
    {
        // set description
        $this->setDescription($contactParams['description']);

        // populate address
        $this->populateAddress($contactParams['address']);
    }

    /**
     * Create address object
     * @param Cmd $command
     * @return Address|null
     */
    private function createAddressObject(Cmd $command)
    {
        if (!empty($command->getOpposerAddress()['addressLine1']) ||
            !empty($command->getOpposerAddress()['addressLine2']) ||
            !empty($command->getOpposerAddress()['addressLine3']) ||
            !empty($command->getOpposerAddress()['addressLine4']) ||
            !empty($command->getOpposerAddress()['town']) ||
            !empty($command->getOpposerAddress()['postcode']) ||
            !empty($command->getOpposerAddress()['countryCode'])
        ) {
            $address = new Address();
            $address->setAddressLine1($command->getAddressLine1());
            $address->setAddressLine2($command->getAddressLine2());
            $address->setAddressLine3($command->getAddressLine3());
            $address->setAddressLine4($command->getAddressLine4());
            $address->setTown($command->getTown());
            $address->setPostcode($command->getPostcode());
            $address->setCountryCode($command->getCountryCode());

            return $address;
        }
        return null;
    }

    /**
     * Generates an ArrayCollection of PhoneContact entities
     *
     * @param Cmd $command
     * @return ArrayCollection|null
     */
    private function createPhoneContactsObjects(Cmd $command)
    {
        $phoneContacts = new ArrayCollection();
        if (!is_null($command->getPhone()))
        {
            $phoneContact = new PhoneContact(PhoneContact::PHONE_CONTACT_TYPE_TEL);
            $phoneContact->setPhoneNumber($command->getPhone());

            $phoneContacts->add($phoneContact);
        }
        if (!is_null($command->getFax()))
        {
            $phoneContact = new PhoneContact(PhoneContact::PHONE_CONTACT_TYPE_TEL);
            $phoneContact->setPhoneNumber($command->getPhone());

            $phoneContacts->add($phoneContact);
        }
        return $phoneContacts;
    }
}
