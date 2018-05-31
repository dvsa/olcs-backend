<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermitCountryLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_country_link",
 *    indexes={
 *        @ORM\Index(name="ecmt_permit_country_link_country_id", columns={"country_id"}),
 *        @ORM\Index(name="ecmt_permit_country_link_permit_id", columns={"ecmt_permit_id"}),
 *        @ORM\Index(name="ecmt_permit_country_link_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_permit_country_link_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtPermitCountryLink extends AbstractEcmtPermitCountryLink
{

}
