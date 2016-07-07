<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Person\Person;

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
    const TRANSPORT_MANAGER_STATUS_DISQUALIFIED = 'tm_s_dis';
    const TRANSPORT_MANAGER_STATUS_REMOVED = 'tm_s_rem';

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
    const CONTACT_TYPE_STATEMENT_REQUESTOR = 'ct_requestor';
    const CONTACT_TYPE_USER = 'ct_user';
    const CONTACT_TYPE_HACKNEY = 'ct_hackney';

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
            case self::CONTACT_TYPE_PARTNER:
                $this->updatePartner($contactParams);
                break;
            case self::CONTACT_TYPE_OBJECTOR:
                $this->updateObjector($contactParams);
                break;
            case self::CONTACT_TYPE_STATEMENT_REQUESTOR:
                $this->updateStatementRequestor($contactParams);
                break;
            case self::CONTACT_TYPE_USER:
                $this->updateUser($contactParams);
                break;
            case self::CONTACT_TYPE_COMPLAINANT:
                $this->updateComplainant($contactParams);
                break;
            case self::CONTACT_TYPE_CORRESPONDENCE_ADDRESS:
                $this->updateCorrespondenceAddress($contactParams);
                break;
        }

        return $this;
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateIrfoOperator(array $contactParams)
    {
        if (isset($contactParams['emailAddress'])) {
            // set email address
            $this->setEmailAddress($contactParams['emailAddress']);
        }

        if (isset($contactParams['address'])) {
            // populate address
            $this->populateAddress($contactParams['address']);
        }

        if (isset($contactParams['phoneContacts'])) {
            // populate phone contacts
            $this->populatePhoneContacts($contactParams['phoneContacts']);
        }
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateObjector(array $contactParams)
    {
        if (isset($contactParams['emailAddress'])) {
            // set email address
            $this->setEmailAddress($contactParams['emailAddress']);
        }

        if (isset($contactParams['description'])) {
            // set description
            $this->setDescription($contactParams['description']);
        }

        if (isset($contactParams['address'])) {
            // populate address
            $this->populateAddress($contactParams['address']);
        }

        if (isset($contactParams['person'])) {
            // populate person
            $this->populatePerson($contactParams['person']);
        }

        if (isset($contactParams['phoneContacts'])) {
            // populate phone contacts
            $this->populatePhoneContacts($contactParams['phoneContacts']);
        }
    }


    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateStatementRequestor(array $contactParams)
    {
        // populate address
        $this->populateAddress($contactParams['address']);

        // populate person
        $this->populatePerson($contactParams['person']);
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
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateUser(array $contactParams)
    {
        // set email address
        $this->setEmailAddress($contactParams['emailAddress']);

        // populate person
        $this->populatePerson($contactParams['person']);

        if (isset($contactParams['address'])) {
            // populate address
            $this->populateAddress($contactParams['address']);
        }

        if (isset($contactParams['phoneContacts'])) {
            // populate phone contacts
            $this->populatePhoneContacts($contactParams['phoneContacts']);
        }
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     */
    private function updateComplainant(array $contactParams)
    {
        // populate address
        $this->populateAddress($contactParams['address']);

        // populate person
        $this->populatePerson($contactParams['person']);
    }

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\OperatorContactDetails
     */
    private function updateCorrespondenceAddress(array $contactParams)
    {
        if (isset($contactParams['emailAddress'])) {
            // set email address
            $this->setEmailAddress($contactParams['emailAddress']);
        }

        if (isset($contactParams['address'])) {
            // populate address
            $this->populateAddress($contactParams['address']);
        }

        // populate phone contacts
        $phoneContacts = array_intersect_key(
            $contactParams,
            array_flip(['mobilePhoneContact', 'businessPhoneContact', 'homePhoneContact', 'faxPhoneContact'])
        );
        $this->populatePhoneContacts($phoneContacts);
    }

    /**
     * Create address object
     * @param array $addressParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\Address
     * @return Address|null
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
            !empty($addressParams['countryCode']) ? $addressParams['countryCode'] : null
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

    /**
     * @param array $personParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\Person
     */
    private function populatePerson(array $personParams)
    {
        if (!($this->person instanceof Person)) {
            $this->person = new Person();
        }

        $this->person->updatePerson(
            $personParams['forename'],
            $personParams['familyName'],
            $this->getDefaultParameter($personParams, 'title'),
            $this->getDefaultParameter($personParams, 'birthDate')
        );
    }

    /**
     * Generates a parameter from either the params array, or returns a default.
     *
     * @param $params
     * @param $var
     * @param null $default
     * @return null
     */
    private function getDefaultParameter($params, $var, $default = null)
    {
        return isset($params[$var]) ? $params[$var] : $default;
    }

    public function updateContactDetailsWithPersonAndEmailAddress($person = null, $emailAddress = null)
    {
        if ($person !== null) {
            $this->setPerson($person);
        }
        $this->setEmailAddress($emailAddress);
    }
}
