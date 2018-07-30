<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="ix_country_id", columns={"id"}),
 *        @ORM\Index(name="ix_country_country_desc", columns={"country_desc"}),
 *        @ORM\Index(name="ix_country_is_member_state", columns={"is_member_state"}),
 *        @ORM\Index(name="ix_country_is_ecmt_state", columns={"is_ecmt_state"}),
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_country_created_on", columns={"created_on"}),
 *        @ORM\Index(name="ix_country_last_modified_on", columns={"last_modified_on"}),
 *        @ORM\Index(name="ix_country_version", columns={"version"}),
 *    }
 * )
 */
class Country extends AbstractCountry
{

}
