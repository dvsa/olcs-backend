<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sectors Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sectors",
 *    indexes={
 *        @ORM\Index(name="permit_sectors_created_by", columns={"created_by"}),
 *        @ORM\Index(name="permit_sectors_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Sectors extends AbstractSectors
{

}
