<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sectors Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="ix_permit_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_permit_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Countrys extends AbstractSectors
{

}
