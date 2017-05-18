<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
 *        @ORM\UniqueConstraint(
 *            name="uk_phone_contact_olbs_key_olbs_type_phone_contact_type",
 *            columns={"olbs_key","olbs_type","phone_contact_type"}
*         )
 *    }
 * )
 */
class PhoneContact extends AbstractPhoneContact
{
    /** @todo REMOVE THESE CONSTANTS after completing this story */
    const TYPE_BUSINESS = 'phone_t_tel';
    const TYPE_HOME = 'phone_t_home';
    const TYPE_MOBILE = 'phone_t_mobile';
    const TYPE_FAX = 'phone_t_fax';

    const TYPE_PRIMARY = 'phone_t_primary';
    const TYPE_SECONDARY = 'phone_t_secondary';

    public function __construct(RefData $phoneContactType)
    {
        $this->phoneContactType = $phoneContactType;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedValues()
    {
        return [
            'contactDetails' => null,
        ];
    }
}
