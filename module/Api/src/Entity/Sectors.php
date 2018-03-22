<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sectors Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sectors",
 *    indexes={
 *        @ORM\Index(name="ecmt_sectors_created_by", columns={"created_by"})
 *    }
 * )
 */
class Sectors extends AbstractSectors
{

}
