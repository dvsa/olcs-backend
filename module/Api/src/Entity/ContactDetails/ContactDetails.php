<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;

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
    const CONTACT_TYPE_REGISTERED_ADDRESS = 'ct_reg';
    const CONTACT_TYPE_COMPLAINANT = 'ct_complainant';
    const CONTACT_TYPE_WORKSHOP = 'ct_work';
    const CONTACT_TYPE_IRFO_OPERATOR = 'ct_irfo_op';

    public function __construct(RefData $contactType)
    {
        parent::__construct();
        $this->setContactType($contactType);
    }

    /**
     * @param RefData $contactType
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     * @return ContactDetails
     */
    public static function create(RefData $contactType, array $contactParams)
    {
        $contactDetails = new static($contactType);
        $contactDetails->update($contactParams);

        return $contactDetails;
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
        }

        return $this;
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateIrfoOperator(array $contactParams)
    {
        if ($contactParams['emailAddress'] !== null) {
            // set email address
            $this->setEmailAddress($contactParams['emailAddress']);
        }

        if ($contactParams['address'] !== null) {
            // populate address
            $this->populateAddress($contactParams['address']);
        }

        if ($contactParams['phoneContacts'] !== null) {
            // populate phone contacts
            $this->populatePhoneContacts($contactParams['phoneContacts']);
        }
    }

    /**
     * @param array $addressParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\Address
     */
    private function populateAddress(array $addressParams)
    {
        if (!($this->address instanceof Address)) {
            $this->address = new Address();
        }

        $this->address->updateAddress(
            $addressParams['addressLine1'],
            $addressParams['addressLine2'],
            $addressParams['addressLine3'],
            $addressParams['addressLine4'],
            $addressParams['town'],
            $addressParams['postcode'],
            $addressParams['countryCode']
        );
    }

    /**
     * @param array $phoneContacts List of Dvsa\Olcs\Transfer\Command\Partial\PhoneContact
     * @return array
     */
    private function populatePhoneContacts(array $phoneContacts)
    {
        $seen = [];

        $collection = $this->getPhoneContacts()->toArray();

        foreach ($phoneContacts as $phoneContact) {
            if (empty($phoneContact['phoneNumber'])) {
                // filter out empty values
                continue;
            }

            if (isset($phoneContact['id']) && !empty($collection[$phoneContact['id']])) {
                // update
                $phoneContactEntity = $collection[$phoneContact['id']];
                $phoneContactEntity->setPhoneNumber($phoneContact['phoneNumber']);

                $seen[$phoneContact['id']] = $phoneContact['id'];
            } else {
                // create
                $phoneContactEntity = new PhoneContact($phoneContact['phoneContactType']);
                $phoneContactEntity->setContactDetails($this);
                $phoneContactEntity->setPhoneNumber($phoneContact['phoneNumber']);

                $this->phoneContacts->add($phoneContactEntity);
            }
        }

        // remove the rest
        foreach (array_diff_key($collection, $seen) as $key => $entity) {
            // unlink
            $this->phoneContacts->remove($key);
        }
    }
}
