<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Country extends AbstractCountry
{

}
