<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;
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

    public static function createForIrfoOperator(
        RefData $contactType,
        Address $address = null,
        $phoneContacts = null,
        $emailAddress = null
    ) {
        $contactDetails = new static($contactType);
        $contactDetails->updateForIrfoOperator($address, $phoneContacts, $emailAddress);

        return $contactDetails;
    }

    public function updateForIrfoOperator(Address $address = null, $phoneContacts = null, $emailAddress = null)
    {
        if ($address !== null) {
            $this->address = $address;
        }
        if ($phoneContacts !== null) {
            $this->phoneContacts = $phoneContacts;
        }
        if ($emailAddress !== null) {
            $this->emailAddress = $emailAddress;
        }

        return $this;
    }
}
