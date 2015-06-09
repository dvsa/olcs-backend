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
    const CONTACT_TYPE_REGISTERED_ADDRESS = 'ct_reg';
    const CONTACT_TYPE_COMPLAINANT = 'ct_complainant';
    const CONTACT_TYPE_ESTABLISHMENT_ADDRESS = 'ct_est';
    const CONTACT_TYPE_CORRESPONDENCE_ADDRESS = 'ct_corr';
    const CONTACT_TYPE_TRANSPORT_CONSULTANT = 'ct_tcon';
    const CONTACT_TYPE_TRANSPORT_MANAGER = 'ct_tm';

    public function __construct(RefData $contactType)
    {
        parent::__construct();

        $this->setContactType($contactType);
    }
}
