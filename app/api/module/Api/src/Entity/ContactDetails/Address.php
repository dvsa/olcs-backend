<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="address",
 *    indexes={
 *        @ORM\Index(name="ix_address_country_code", columns={"country_code"}),
 *        @ORM\Index(name="ix_address_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_address_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_address_admin_area", columns={"admin_area"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_address_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Address extends AbstractAddress
{
    public function updateAddress(
        $addressLine1 = null,
        $addressLine2 = null,
        $addressLine3 = null,
        $addressLine4 = null,
        $town = null,
        $postcode = null,
        Country $countryCode = null
    ) {
        $this->setAddressLine1($addressLine1);
        $this->setAddressLine2($addressLine2);
        $this->setAddressLine3($addressLine3);
        $this->setAddressLine4($addressLine4);
        $this->setTown($town);
        $this->setPostcode($postcode);
        $this->setCountryCode($countryCode);
    }
}
