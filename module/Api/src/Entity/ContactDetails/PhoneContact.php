<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhoneContact Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="phone_contact",
 *    indexes={
 *        @ORM\Index(name="ix_phone_contact_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_phone_contact_phone_contact_type", columns={"phone_contact_type"}),
 *        @ORM\Index(name="ix_phone_contact_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_phone_contact_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_phone_contact_olbs_key_olbs_type_phone_contact_type", columns={"olbs_key","olbs_type","phone_contact_type"})
 *    }
 * )
 */
class PhoneContact extends AbstractPhoneContact
{

}
